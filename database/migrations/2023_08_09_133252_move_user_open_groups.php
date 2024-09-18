<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\User;

class MoveUserOpenGroups extends Migration
{
    const IN_MAX = 999;

    private array $userIds = [];
    private array $updates = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'open_device_groups')) {
            return;
        }

        $this->moveGroups(['geofence_groups', 'device_groups'], ['open_geofence_groups', 'open_device_groups']);

        Schema::table('users', function (Blueprint $table) {
            $table->text('ungrouped_open')->after('sms_gateway_params');
        });

        DB::statement("UPDATE users SET ungrouped_open = CONCAT('{\"geofence_group\":', open_geofence_groups, ',\"device_group\":', open_device_groups, ',\"poi_group\":1}')");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('open_geofence_groups');
            $table->dropColumn('open_device_groups');
        });
    }

    private function moveGroups(array $dstTables, array $userProperties): void
    {
        $ids = [];

        foreach ($dstTables as $dstTable) {
            Schema::table($dstTable, function (Blueprint $table) {
                $table->boolean('open')->default(false);
            });

            $ids[$dstTable] = [];
        }

        foreach ($userProperties as $userProperty) {
            $this->userIds[$userProperty] = ['open' => [], 'closed' => []];
        }

        $users = User::select(['id', ...$userProperties])->cursor();

        foreach ($users as $user) {
            foreach ($userProperties as $key => $userProperty) {
                $openGroups = json_decode($user->$userProperty, true) ?: [];

                $setting = in_array(0, $openGroups) ? 'open' : 'closed';

                $this->userIds[$userProperty][$setting][] = $user->id;

                if (count($this->userIds[$userProperty][$setting]) === self::IN_MAX) {
                    $this->updateUsersUngrouped($userProperty, $setting);
                }

                $table = $dstTables[$key];
                $ids[$table] = array_merge($ids[$table], $openGroups);

                while (count($ids[$table]) > self::IN_MAX) {
                    $updateIds = array_splice($ids[$table], 0, self::IN_MAX);

                    $this->updates[] = [DB::table($table)->whereIn('id', $updateIds), ['open' => 1]];
                }

                $this->runUpdates(100);
            }
        }

        foreach ($dstTables as $dstTable) {
            if (count($ids[$dstTable])) {
                $this->updates[] = [DB::table($dstTable)->whereIn('id', $ids[$dstTable]), ['open' => 1]];
            }
        }

        $this->runUpdates();

        foreach ($userProperties as $userProperty) {
            $this->updateUsersUngrouped($userProperty, 'open');
            $this->updateUsersUngrouped($userProperty, 'closed');
        }
    }

    private function updateUsersUngrouped(string $userProperty, string $setting): void
    {
        $ids = $this->userIds[$userProperty][$setting];

        if (count($ids) === 0) {
            return;
        }

        $value = (int)($setting === 'open');

        DB::table('users')->whereIn('id', $ids)->update([$userProperty => $value]);

        $this->userIds[$userProperty][$setting] = [];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'ungrouped_open')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->text('open_device_groups')->nullable()->after('sms_gateway_params');
            $table->text('open_geofence_groups')->nullable()->after('sms_gateway_params');
        });

        $this->revertGroups('geofence_groups', 'geofence_group');
        $this->revertGroups('device_groups', 'device_group');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ungrouped_open');
        });
    }

    private function revertGroups(string $table, string $userProperty): void
    {
        $groups = DB::query()
            ->select(['users.id AS user_id', "$table.id", 'users.ungrouped_open'])
            ->from('users')
            ->leftJoin($table, "$table.user_id", 'users.id')
            ->where(function (Builder $query) use ($table) {
                $query->where("$table.open", 1)->orWhereNull("$table.id");
            })
            ->orderBy('users.id')
            ->cursor();

        $prevGroup = $groups->current();
        $groupIds = [];

        foreach ($groups as $group) {
            if ($group->user_id === $prevGroup->user_id) {
                if ($group->id !== null) {
                    $groupIds[] = $group->id;
                }

                continue;
            }

            $this->storeUserGroups($prevGroup, $userProperty, $groupIds);

            if ($group->id !== null) {
                $groupIds[] = $group->id;
            }

            $prevGroup = $group;
        }

        if (isset($group)) {
            $this->storeUserGroups($group, $userProperty, $groupIds);
            $this->runUpdates();
        }

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('open');
        });
    }

    private function storeUserGroups($group, string $userProperty, array &$groupIds): void
    {
        if (!empty(json_decode($group->ungrouped_open)->$userProperty)) {
            $groupIds[] = 0;
        }

        $groups = json_encode($groupIds);
        $userProperty = 'open_' . $userProperty . 's';

        $this->updates[] = [DB::table('users')->where('id', $group->user_id), [$userProperty => $groups]];

        $this->runUpdates(500);

        $groupIds = [];
    }

    private function runUpdates(int $min = 0): void
    {
        if (count($this->updates) < $min) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->updates as $update) {
                $update[0]->update($update[1]);
            }
        });

        $this->updates = [];
    }
}
