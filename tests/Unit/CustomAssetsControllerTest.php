<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\CustomAssetsController as CustomAssetsController;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class CustomAssetsControllerTest extends TestCase
{
    /****/
    protected $baseUrl = 'http://localhost';
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        Auth::loginUsingId(1, true);
        $this->controller = new CustomAssetsController();
    }

    /** @test */
    public function get_correct_asset_edit_page()
    {
        $this->call('GET', 'admin/custom/js');

        $this->assertResponseOk();
        $this->assertViewHas('js');
    }

    /** @test */
    public function which_file_is_loaded()
    {
        $file = $this->controller->whichAssetFile('css');

        $this->assertEquals(storage_path('custom/css.css'), $file);
    }

    /** @test */
    public function exception_on_wrong_asset_request()
    {
        $this->setExpectedException(RouteNotFoundException::class);
        $this->controller->whichAssetFile('bad_asset');
    }

    public function custom_asset_stored_in_a_file()
    {
        $script = 'function foo(){return "bar";}';
        $this->call('POST', 'admin/custom/js', ['js' => $script]);

        $this->assertResponseOk();
        $this->assertViewHas($script);

        $folderExists = !File::isDirectory(storage_path('custom')) ? false : true;

        $this->assertTrue($folderExists);

    }
}
