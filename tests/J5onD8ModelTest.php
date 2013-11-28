<?php

require_once(__DIR__.'/../src/J5onD8.php');
require_once(__DIR__.'/J5onD8TestUser.php');
require_once(__DIR__.'/J5onD8TestUserWithoutPrivateFlag.php');

class J5onD8ModelTest extends PHPUnit_Framework_TestCase
{
	function setUp()
    {

    	$var_dir = __DIR__.'/var/';
        J5onD8::$public_dir = $var_dir.'public/';
        J5onD8::$private_dir = $var_dir.'private/';

        @unlink(J5onD8::$public_dir.'/j5on_d8test_user.json');
        @unlink(J5onD8::$private_dir.'/j5on_d8test_user.json');
    }

    function testGetJsonFile()
    {
        J5onD8::$public_dir = 'foo/';
        J5onD8TestUser::$is_private = false;
        $this->assertEquals(
            'foo/j5on_d8test_user.json',
            J5onD8TestUser::getJsonFile()
        );

        J5onD8::$private_dir = 'bar/';
        J5onD8TestUser::$is_private = true;
        $this->assertEquals(
            'bar/j5on_d8test_user.json',
            J5onD8TestUser::getJsonFile()
        );

        $this->assertEquals(
            'foo/j5on_d8test_user_without_private_flag.json',
            J5onD8TestUserWithoutPrivateFlag::getJsonFile()
        );

    }

	function testLoad_Private()
    {
    	$users = J5onD8TestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testLoad_Public()
    {
    	J5onD8TestUser::$is_private = false;
    	$users = J5onD8TestUser::load();
    	$this->assertEquals(0, count($users));
    }

    function testSave_Private()
    {
        J5onD8TestUser::$is_private = true;
    	$users = J5onD8TestUser::load();
    	$user = new J5onD8TestUser;
    	$user->name = 'foo';
    	$users['testSave_Private'] = $user;
    	$users->save();

		J5onD8TestUser::$is_private = false;
        $this->assertEquals(0, count(J5onD8TestUser::load()));

        J5onD8TestUser::$is_private = true;
    	$users = J5onD8TestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Private']->name);
    }

    function testSave_Public()
    {
        J5onD8TestUser::$is_private = false;
    	$users = J5onD8TestUser::load();
    	$user = new J5onD8TestUser;
    	$user->name = 'foo';
    	$users['testSave_Public'] = $user;
    	$users->save();

        J5onD8TestUser::$is_private = true;
        $this->assertEquals(0, count(J5onD8TestUser::load()));

        J5onD8TestUser::$is_private = false;
    	$users = J5onD8TestUser::load();
    	$this->assertEquals(1, count($users));
    	$this->assertEquals('foo', $users['testSave_Public']->name);
    }

    function testSave_Escaping()
    {
        $users = J5onD8TestUser::load();
        $user = new J5onD8TestUser;
        $user->name = '<>"\"\'{}';
        $users['testSave_Escaping'] = $user;
        $users->save();

        $loaded_users = J5onD8TestUser::load();
        $this->assertEquals($user->name, $loaded_users['testSave_Escaping']->name);
    }
}