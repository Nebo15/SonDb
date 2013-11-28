<?php

require_once(__DIR__.'/../src/BrianDb.php');
require_once(__DIR__.'/BrianDbTestUser.php');
require_once(__DIR__.'/BrianDbTestUserWithoutPrivateFlag.php');

class BrianDbModelTest extends PHPUnit_Framework_TestCase
{
	function setUp()
    {

    	$var_dir = __DIR__.'/var/';
        BrianDb::$public_dir = $var_dir.'public/';
        BrianDb::$private_dir = $var_dir.'private/';

        @unlink(BrianDb::$public_dir.'/brian_db_test_user.json');
        @unlink(BrianDb::$private_dir.'/brian_db_test_user.json');
    }

    function testGetJsonFile()
    {
        BrianDb::$public_dir = 'foo/';
        BrianDbTestUser::$is_private = false;
        $this->assertEquals(
            'foo/brian_db_test_user.json',
            BrianDbTestUser::getJsonFile()
        );

        BrianDb::$private_dir = 'bar/';
        BrianDbTestUser::$is_private = true;
        $this->assertEquals(
            'bar/brian_db_test_user.json',
            BrianDbTestUser::getJsonFile()
        );

        $this->assertEquals(
            'foo/brian_db_test_user_without_private_flag.json',
            BrianDbTestUserWithoutPrivateFlag::getJsonFile()
        );

    }

	function testLoad_Private()
    {
    	$users = BrianDbTestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testLoad_Public()
    {
    	BrianDbTestUser::$is_private = false;
    	$users = BrianDbTestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testSave_Private()
    {
        BrianDbTestUser::$is_private = true;
    	$users = BrianDbTestUser::load();
    	$user = new BrianDbTestUser;
    	$user->name = 'foo';
    	$users['testSave_Private'] = $user;
    	$users->save();

		BrianDbTestUser::$is_private = false;
        $this->assertEquals(0, count(BrianDbTestUser::load()));

        BrianDbTestUser::$is_private = true;
    	$users = BrianDbTestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Private']->name);
    }

    function testSave_Public()
    {
        BrianDbTestUser::$is_private = false;
    	$users = BrianDbTestUser::load();
    	$user = new BrianDbTestUser;
    	$user->name = 'foo';
    	$users['testSave_Public'] = $user;
    	$users->save();

        BrianDbTestUser::$is_private = true;
        $this->assertEquals(0, count(BrianDbTestUser::load()));

        BrianDbTestUser::$is_private = false;
    	$users = BrianDbTestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Public']->name);
    }

    function testSave_Escaping()
    {
        $users = BrianDbTestUser::load();
        $user = new BrianDbTestUser;
        $user->name = '<>"\"\'{}';
        $users['testSave_Escaping'] = $user;
        $users->save();

        $loaded_users = BrianDbTestUser::load();
        $this->assertEquals($user->name, $loaded_users['testSave_Escaping']->name);
    }
}