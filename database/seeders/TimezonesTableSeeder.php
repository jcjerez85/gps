<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;

class TimezonesTableSeeder extends Seeder {
    /**
     * @var Timezone
     */
    private $timezone;

    public function __construct(Timezone $timezone)
    {
        $this->timezone = $timezone;
    }

	public function run()
	{
        $items = [
            ['order' => '1', 'title' => 'UTC -14:00', 'zone' => '-14hours', 'prefix' => 'minus', 'time' => '14 0'],
            ['order' => '1.1', 'title' => 'UTC -13:45', 'zone' => '-13hours -45minutes', 'prefix' => 'minus', 'time' => '13 45'],
            ['order' => '1.2', 'title' => 'UTC -13:30', 'zone' => '-13hours -30minutes', 'prefix' => 'minus', 'time' => '13 30'],
            ['order' => '1.3', 'title' => 'UTC -13:15', 'zone' => '-13hours -15minutes', 'prefix' => 'minus', 'time' => '13 15'],
            ['order' => '1.4', 'title' => 'UTC -13:00', 'zone' => '-13hours', 'prefix' => 'minus', 'time' => '13 0'],
            ['order' => '1.5', 'title' => 'UTC -12:45', 'zone' => '-12hours -45minutes', 'prefix' => 'minus', 'time' => '12 45'],
            ['order' => '1.6', 'title' => 'UTC -12:30', 'zone' => '-12hours -30minutes', 'prefix' => 'minus', 'time' => '12 30'],
            ['order' => '1.7', 'title' => 'UTC -12:15', 'zone' => '-12hours -15minutes', 'prefix' => 'minus', 'time' => '12 15'],
            ['order' => '2', 'title' => 'UTC -12:00', 'zone' => '-12hours', 'prefix' => 'minus', 'time' => '12 0'],
            ['order' => '2.1', 'title' => 'UTC -11:45', 'zone' => '-11hours -45minutes', 'prefix' => 'minus', 'time' => '11 45'],
            ['order' => '2.2', 'title' => 'UTC -11:30', 'zone' => '-11hours -30minutes', 'prefix' => 'minus', 'time' => '11 30'],
            ['order' => '2.3', 'title' => 'UTC -11:15', 'zone' => '-11hours -15minutes', 'prefix' => 'minus', 'time' => '11 15'],
            ['order' => '3', 'title' => 'UTC -11:00', 'zone' => '-11hours', 'prefix' => 'minus', 'time' => '11 0'],
            ['order' => '3.1', 'title' => 'UTC -10:45', 'zone' => '-10hours -45minutes', 'prefix' => 'minus', 'time' => '10 45'],
            ['order' => '4', 'title' => 'UTC -10:30', 'zone' => '-10hours -30minutes', 'prefix' => 'minus', 'time' => '10 30'],
            ['order' => '4.1', 'title' => 'UTC -10:15', 'zone' => '-10hours -15minutes', 'prefix' => 'minus', 'time' => '10 15'],
            ['order' => '4.2', 'title' => 'UTC -10:00', 'zone' => '-10hours', 'prefix' => 'minus', 'time' => '10 0'],
            ['order' => '4.2', 'title' => 'UTC -9:45', 'zone' => '-9hours -45minutes', 'prefix' => 'minus', 'time' => '9 45'],
            ['order' => '4.3', 'title' => 'UTC -9:30', 'zone' => '-9hours -30minutes', 'prefix' => 'minus', 'time' => '9 30'],
            ['order' => '4.4', 'title' => 'UTC -9:15', 'zone' => '-9hours -15minutes', 'prefix' => 'minus', 'time' => '9 15'],
            ['order' => '5', 'title' => 'UTC -9:00', 'zone' => '-9hours', 'prefix' => 'minus', 'time' => '9 0'],
            ['order' => '5.1', 'title' => 'UTC -8:45', 'zone' => '-8hours -45minutes', 'prefix' => 'minus', 'time' => '8 45'],
            ['order' => '5.2', 'title' => 'UTC -8:30', 'zone' => '-8hours -30minutes', 'prefix' => 'minus', 'time' => '8 30'],
            ['order' => '5.3', 'title' => 'UTC -8:15', 'zone' => '-8hours -15minutes', 'prefix' => 'minus', 'time' => '8 15'],
            ['order' => '6', 'title' => 'UTC -8:00', 'zone' => '-8hours', 'prefix' => 'minus', 'time' => '8 0'],
            ['order' => '6.1', 'title' => 'UTC -7:45', 'zone' => '-7hours -45minutes', 'prefix' => 'minus', 'time' => '7 45'],
            ['order' => '6.2', 'title' => 'UTC -7:30', 'zone' => '-7hours -30minutes', 'prefix' => 'minus', 'time' => '7 30'],
            ['order' => '6.3', 'title' => 'UTC -7:15', 'zone' => '-7hours -15minutes', 'prefix' => 'minus', 'time' => '7 15'],
            ['order' => '7', 'title' => 'UTC -7:00', 'zone' => '-7hours', 'prefix' => 'minus', 'time' => '7 0'],
            ['order' => '7.1', 'title' => 'UTC -6:45', 'zone' => '-6hours -45minutes', 'prefix' => 'minus', 'time' => '6 45'],
            ['order' => '7.2', 'title' => 'UTC -6:30', 'zone' => '-6hours -30minutes', 'prefix' => 'minus', 'time' => '6 30'],
            ['order' => '7.3', 'title' => 'UTC -6:15', 'zone' => '-6hours -15minutes', 'prefix' => 'minus', 'time' => '6 15'],
            ['order' => '8', 'title' => 'UTC -6:00', 'zone' => '-6hours', 'prefix' => 'minus', 'time' => '6 0'],
            ['order' => '8.1', 'title' => 'UTC -5:45', 'zone' => '-5hours -45minutes', 'prefix' => 'minus', 'time' => '5 45'],
            ['order' => '8.2', 'title' => 'UTC -5:30', 'zone' => '-5hours -30minutes', 'prefix' => 'minus', 'time' => '5 30'],
            ['order' => '8.3', 'title' => 'UTC -5:15', 'zone' => '-5hours -15minutes', 'prefix' => 'minus', 'time' => '5 15'],
            ['order' => '9', 'title' => 'UTC -5:00', 'zone' => '-5hours', 'prefix' => 'minus', 'time' => '5 0'],
            ['order' => '9.1', 'title' => 'UTC -4:45', 'zone' => '-4hours -45minutes', 'prefix' => 'minus', 'time' => '4 45'],
            ['order' => '10', 'title' => 'UTC -4:30', 'zone' => '-4hours -30minutes', 'prefix' => 'minus', 'time' => '4 30'],
            ['order' => '10.1', 'title' => 'UTC -4:15', 'zone' => '-4hours -15minutes', 'prefix' => 'minus', 'time' => '4 15'],
            ['order' => '11', 'title' => 'UTC -4:00', 'zone' => '-4hours', 'prefix' => 'minus', 'time' => '4 0'],
            ['order' => '11.1', 'title' => 'UTC -3:45', 'zone' => '-3hours -45minutes', 'prefix' => 'minus', 'time' => '3 45'],
            ['order' => '12', 'title' => 'UTC -3:30', 'zone' => '-3hours -30minutes', 'prefix' => 'minus', 'time' => '3 30'],
            ['order' => '12.1', 'title' => 'UTC -3:15', 'zone' => '-3hours -15minutes', 'prefix' => 'minus', 'time' => '3 15'],
            ['order' => '13', 'title' => 'UTC -3:00', 'zone' => '-3hours', 'prefix' => 'minus', 'time' => '3 0'],
            ['order' => '13.1', 'title' => 'UTC -2:45', 'zone' => '-2hours -45minutes', 'prefix' => 'minus', 'time' => '2 45'],
            ['order' => '13.2', 'title' => 'UTC -2:30', 'zone' => '-2hours -30minutes', 'prefix' => 'minus', 'time' => '2 30'],
            ['order' => '13.3', 'title' => 'UTC -2:15', 'zone' => '-2hours -15minutes', 'prefix' => 'minus', 'time' => '2 15'],
            ['order' => '14', 'title' => 'UTC -2:00', 'zone' => '-2hours', 'prefix' => 'minus', 'time' => '2 0'],
            ['order' => '14.1', 'title' => 'UTC -1:45', 'zone' => '-1hours -45minutes', 'prefix' => 'minus', 'time' => '1 45'],
            ['order' => '14.2', 'title' => 'UTC -1:30', 'zone' => '-1hours -30minutes', 'prefix' => 'minus', 'time' => '1 30'],
            ['order' => '14.3', 'title' => 'UTC -1:15', 'zone' => '-1hours -15minutes', 'prefix' => 'minus', 'time' => '1 15'],
            ['order' => '15', 'title' => 'UTC -1:00', 'zone' => '-1hours', 'prefix' => 'minus', 'time' => '1 0'],
            ['order' => '15.1', 'title' => 'UTC -0:45', 'zone' => '-0hours -45minutes', 'prefix' => 'minus', 'time' => '0 45'],
            ['order' => '15.2', 'title' => 'UTC -0:30', 'zone' => '-0hours -30minutes', 'prefix' => 'minus', 'time' => '0 30'],
            ['order' => '15.3', 'title' => 'UTC -0:15', 'zone' => '-0hours -15minutes', 'prefix' => 'minus', 'time' => '0 15'],
            ['order' => '16', 'title' => 'UTC 00:00', 'zone' => '+0hours', 'prefix' => 'plus', 'time' => '0 0'],
            ['order' => '16.1', 'title' => 'UTC +0:15', 'zone' => '+0hours +15minutes', 'prefix' => 'plus', 'time' => '0 15'],
            ['order' => '17', 'title' => 'UTC +0:30', 'zone' => '+0hours +30minutes', 'prefix' => 'plus', 'time' => '0 30'],
            ['order' => '17.1', 'title' => 'UTC +0:45', 'zone' => '+0hours +45minutes', 'prefix' => 'plus', 'time' => '0 45'],
            ['order' => '18', 'title' => 'UTC +1:00', 'zone' => '+1hours', 'prefix' => 'plus', 'time' => '1 0'],
            ['order' => '18.1', 'title' => 'UTC +1:15', 'zone' => '+1hours +15minutes', 'prefix' => 'plus', 'time' => '1 15'],
            ['order' => '18.2', 'title' => 'UTC +1:30', 'zone' => '+1hours +30minutes', 'prefix' => 'plus', 'time' => '1 30'],
            ['order' => '18.3', 'title' => 'UTC +1:45', 'zone' => '+1hours +45minutes', 'prefix' => 'plus', 'time' => '1 45'],
            ['order' => '19', 'title' => 'UTC +2:00', 'zone' => '+2hours', 'prefix' => 'plus', 'time' => '2 0'],
            ['order' => '19.1', 'title' => 'UTC +2:15', 'zone' => '+2hours +15minutes', 'prefix' => 'plus', 'time' => '2 15'],
            ['order' => '19.2', 'title' => 'UTC +2:30', 'zone' => '+2hours +30minutes', 'prefix' => 'plus', 'time' => '2 30'],
            ['order' => '19.3', 'title' => 'UTC +2:45', 'zone' => '+2hours +45minutes', 'prefix' => 'plus', 'time' => '2 45'],
            ['order' => '20', 'title' => 'UTC +3:00', 'zone' => '+3hours', 'prefix' => 'plus', 'time' => '3 0'],
            ['order' => '20.1', 'title' => 'UTC +3:15', 'zone' => '+3hours +15minutes', 'prefix' => 'plus', 'time' => '3 15'],
            ['order' => '21', 'title' => 'UTC +3:30', 'zone' => '+3hours +30minutes', 'prefix' => 'plus', 'time' => '3 30'],
            ['order' => '21.1', 'title' => 'UTC +3:45', 'zone' => '+3hours +45minutes', 'prefix' => 'plus', 'time' => '3 45'],
            ['order' => '22', 'title' => 'UTC +4:00', 'zone' => '+4hours', 'prefix' => 'plus', 'time' => '4 0'],
            ['order' => '22.1', 'title' => 'UTC +4:15', 'zone' => '+4hours +15minutes', 'prefix' => 'plus', 'time' => '4 15'],
            ['order' => '23', 'title' => 'UTC +4:30', 'zone' => '+4hours +30minutes', 'prefix' => 'plus', 'time' => '4 30'],
            ['order' => '24', 'title' => 'UTC +4:45', 'zone' => '+4hours +45minutes', 'prefix' => 'plus', 'time' => '4 45'],
            ['order' => '25', 'title' => 'UTC +5:00', 'zone' => '+5hours', 'prefix' => 'plus', 'time' => '5 0'],
            ['order' => '25.1', 'title' => 'UTC +5:15', 'zone' => '+5hours +15minutes', 'prefix' => 'plus', 'time' => '5 15'],
            ['order' => '26', 'title' => 'UTC +5:30', 'zone' => '+5hours +30minutes', 'prefix' => 'plus', 'time' => '5 30'],
            ['order' => '27', 'title' => 'UTC +5:45', 'zone' => '+5hours +45minutes', 'prefix' => 'plus', 'time' => '5 45'],
            ['order' => '28', 'title' => 'UTC +6:00', 'zone' => '+6hours', 'prefix' => 'plus', 'time' => '6 0'],
            ['order' => '28.1', 'title' => 'UTC +6:15', 'zone' => '+6hours +15minutes', 'prefix' => 'plus', 'time' => '6 15'],
            ['order' => '29', 'title' => 'UTC +6:30', 'zone' => '+6hours +30minutes', 'prefix' => 'plus', 'time' => '6 30'],
            ['order' => '29.1', 'title' => 'UTC +6:45', 'zone' => '+6hours +45minutes', 'prefix' => 'plus', 'time' => '6 45'],
            ['order' => '30', 'title' => 'UTC +7:00', 'zone' => '+7hours', 'prefix' => 'plus', 'time' => '7 0'],
            ['order' => '30.1', 'title' => 'UTC +7:15', 'zone' => '+7hours +15minutes', 'prefix' => 'plus', 'time' => '7 15'],
            ['order' => '30.2', 'title' => 'UTC +7:30', 'zone' => '+7hours +30minutes', 'prefix' => 'plus', 'time' => '7 30'],
            ['order' => '30.3', 'title' => 'UTC +7:45', 'zone' => '+7hours +45minutes', 'prefix' => 'plus', 'time' => '7 45'],
            ['order' => '31', 'title' => 'UTC +8:00', 'zone' => '+8hours', 'prefix' => 'plus', 'time' => '8 0'],
            ['order' => '31.1', 'title' => 'UTC +8:15', 'zone' => '+8hours +15minutes', 'prefix' => 'plus', 'time' => '8 15'],
            ['order' => '31.2', 'title' => 'UTC +8:30', 'zone' => '+8hours +30minutes', 'prefix' => 'plus', 'time' => '8 30'],
            ['order' => '31.3', 'title' => 'UTC +8:45', 'zone' => '+8hours +45minutes', 'prefix' => 'plus', 'time' => '8 45'],
            ['order' => '32', 'title' => 'UTC +9:00', 'zone' => '+9hours', 'prefix' => 'plus', 'time' => '9 0'],
            ['order' => '32.1', 'title' => 'UTC +9:15', 'zone' => '+9hours +15minutes', 'prefix' => 'plus', 'time' => '9 15'],
            ['order' => '32.2', 'title' => 'UTC +9:30', 'zone' => '+9hours +30minutes', 'prefix' => 'plus', 'time' => '9 30'],
            ['order' => '32.3', 'title' => 'UTC +9:45', 'zone' => '+9hours +45minutes', 'prefix' => 'plus', 'time' => '9 45'],
            ['order' => '33', 'title' => 'UTC +10:00', 'zone' => '+10hours', 'prefix' => 'plus', 'time' => '10 0'],
            ['order' => '33.1', 'title' => 'UTC +10:15', 'zone' => '+10hours +15minutes', 'prefix' => 'plus', 'time' => '10 15'],
            ['order' => '34', 'title' => 'UTC +10:30', 'zone' => '+10hours +30minutes', 'prefix' => 'plus', 'time' => '10 30'],
            ['order' => '34.1', 'title' => 'UTC +10:45', 'zone' => '+10hours +45minutes', 'prefix' => 'plus', 'time' => '10 45'],
            ['order' => '35', 'title' => 'UTC +11:00', 'zone' => '+11hours', 'prefix' => 'plus', 'time' => '11 0'],
            ['order' => '35.1', 'title' => 'UTC +11:15', 'zone' => '+11hours +15minutes', 'prefix' => 'plus', 'time' => '11 15'],
            ['order' => '35.2', 'title' => 'UTC +11:30', 'zone' => '+11hours +30minutes', 'prefix' => 'plus', 'time' => '11 30'],
            ['order' => '35.3', 'title' => 'UTC +11:45', 'zone' => '+11hours +45minutes', 'prefix' => 'plus', 'time' => '11 45'],
            ['order' => '36', 'title' => 'UTC +12:00', 'zone' => '+12hours', 'prefix' => 'plus', 'time' => '12 0'],
            ['order' => '36.1', 'title' => 'UTC +12:15', 'zone' => '+12hours +15minutes', 'prefix' => 'plus', 'time' => '12 15'],
            ['order' => '36.2', 'title' => 'UTC +12:30', 'zone' => '+12hours +30minutes', 'prefix' => 'plus', 'time' => '12 30'],
            ['order' => '36.3', 'title' => 'UTC +12:45', 'zone' => '+12hours +45minutes', 'prefix' => 'plus', 'time' => '12 45'],
            ['order' => '37', 'title' => 'UTC +13:00', 'zone' => '+13hours', 'prefix' => 'plus', 'time' => '13 0'],
            ['order' => '37.1', 'title' => 'UTC +13:15', 'zone' => '+13hours +15minutes', 'prefix' => 'plus', 'time' => '13 15'],
            ['order' => '37.2', 'title' => 'UTC +13:30', 'zone' => '+13hours +30minutes', 'prefix' => 'plus', 'time' => '13 30'],
            ['order' => '37.3', 'title' => 'UTC +13:45', 'zone' => '+13hours +45minutes', 'prefix' => 'plus', 'time' => '13 45'],
            ['order' => '38', 'title' => 'UTC +14:00', 'zone' => '+14hours', 'prefix' => 'plus', 'time' => '14 0'],
        ];
        foreach ($items as $item) {
            $this->timezone->create($item);
        }
	}

}