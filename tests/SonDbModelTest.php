<?php

require_once(__DIR__.'/../src/SonDb.php');
require_once(__DIR__.'/SonDbTestUser.php');
require_once(__DIR__.'/SonDbTestUserWithoutPrivateFlag.php');

class SonDbModelTest extends PHPUnit_Framework_TestCase
{
	function setUp()
    {

    	$var_dir = __DIR__.'/var/';
        SonDb::$public_dir = $var_dir.'public/';
        SonDb::$private_dir = $var_dir.'private/';

        @unlink(SonDb::$public_dir.'/son_db_test_user.json');
        @unlink(SonDb::$private_dir.'/son_db_test_user.json');
    }

    function testGetJsonFile()
    {
        SonDb::$public_dir = 'foo/';
        SonDbTestUser::$is_private = false;
        $this->assertEquals(
            'foo/son_db_test_user.json',
            SonDbTestUser::getJsonFile()
        );

        SonDb::$private_dir = 'bar/';
        SonDbTestUser::$is_private = true;
        $this->assertEquals(
            'bar/son_db_test_user.json',
            SonDbTestUser::getJsonFile()
        );

        $this->assertEquals(
            'foo/son_db_test_user_without_private_flag.json',
            SonDbTestUserWithoutPrivateFlag::getJsonFile()
        );

    }

	function testLoad_Private()
    {
    	$users = SonDbTestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testLoad_Public()
    {
    	SonDbTestUser::$is_private = false;
    	$users = SonDbTestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testSave_Private()
    {
        SonDbTestUser::$is_private = true;
    	$users = SonDbTestUser::load();
    	$user = new SonDbTestUser;
    	$user->name = 'foo';
    	$users['testSave_Private'] = $user;
    	$users->save();

		SonDbTestUser::$is_private = false;
        $this->assertEquals(0, count(SonDbTestUser::load()));

        SonDbTestUser::$is_private = true;
    	$users = SonDbTestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Private']->name);
    }

    function testSave_Public()
    {
        SonDbTestUser::$is_private = false;
    	$users = SonDbTestUser::load();
    	$user = new SonDbTestUser;
    	$user->name = 'foo';
    	$users['testSave_Public'] = $user;
    	$users->save();

        SonDbTestUser::$is_private = true;
        $this->assertEquals(0, count(SonDbTestUser::load()));

        SonDbTestUser::$is_private = false;
    	$users = SonDbTestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Public']->name);
    }

    function testSave_Escaping()
    {
        $users = SonDbTestUser::load();
        $user = new SonDbTestUser;
        $user->name = '<>"\"\'{}';
        $users['testSave_Escaping'] = $user;
        $users->save();

        $loaded_users = SonDbTestUser::load();
        $this->assertEquals($user->name, $loaded_users['testSave_Escaping']->name);
    }
}