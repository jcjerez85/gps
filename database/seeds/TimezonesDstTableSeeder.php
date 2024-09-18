<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezonesDstTableSeeder extends Seeder {

    public function __construct()
    {

    }

	public function run()
	{
            DB::statement("INSERT INTO timezones_dst (id, country, from_period, from_time, to_period, to_time) VALUES
(1, 'Akrotiri and Dhekelia(UK)', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(2, 'Albania', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(3, 'Andorra', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(4, 'Australia', 'First Sunday of October', NULL, 'First Sunday of April', NULL),
(5, 'Austria', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(6, 'Bahamas', 'Second Sunday of March', NULL, 'First Sunday November', NULL),
(7, 'Belgium', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(8, 'Bermuda (UK)', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(9, 'Bosnia and Herzegovina', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(10, 'Brazil', 'Third Sunday of October', NULL, 'Third Sunday of February', NULL),
(11, 'Bulgaria', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(12, 'Canada', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(13, 'Chile', 'September 4', NULL, 'April 3', NULL),
(14, 'Croatia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(15, 'Cuba', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(16, 'Cyprus', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(17, 'Czech Republic', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(18, 'Denmark', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(19, 'Egypt', 'July 8', NULL, 'Last friday of October', NULL),
(20, 'Estonia', 'last Sunday of March', NULL, 'last Sunday of October', NULL),
(21, 'Faroe Islands (DK)', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(22, 'Fiji', 'First Sunday of November', NULL, 'Third Sunday of January', NULL),
(23, 'Finland', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(24, 'France', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(25, 'Germany', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(26, 'Greece', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(27, 'Greenland (DK)', 'last Saturday of March', '22:00', 'last Saturday of October', '23:00'),
(28, 'Guernsey (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(29, 'Holy See', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(30, 'Hungary', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(31, 'Iran', 'March 21', NULL, 'September 21', NULL),
(32, 'Ireland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(33, 'Isle of Man (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(34, 'Israel', 'Last Friday of March', NULL, 'Last Friday of October', NULL),
(35, 'Italy', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(36, 'Jersey (UK)', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(37, 'Jordan', 'Last Friday of February', NULL, 'Last Friday of October', NULL),
(38, 'Kosovo', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(39, 'Latvia', 'last Sunday of March', '01:00', 'last Sunday of  October', '01:00'),
(40, 'Lebanon', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(41, 'Liechtenstein', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(42, 'Lithuania', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(43, 'Luxembourg', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(44, 'Macedonia', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(45, 'Malta', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(46, 'Mexico', 'First Sunday of April', NULL, 'Last Sunday of October', NULL),
(47, 'Moldova', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(48, 'Monaco', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(49, 'Mongolia', 'Last Saturday of March', NULL, 'Last Saturday of September', NULL),
(50, 'Montenegro', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(51, 'Morocco', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(52, 'Namibia', 'First Sunday of September', NULL, 'First Sunday of April', NULL),
(53, 'Netherlands', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(54, 'New Zealand', 'Last Sunday of September', NULL, 'First Sunday of April', NULL),
(55, 'Norway', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(56, 'Paraguay', 'First Sunday of October', NULL, 'Fourth Sunday of March', NULL),
(57, 'Poland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(58, 'Portugal', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(59, 'Romania', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(60, 'Saint Pierre and Miquelon?(FR)', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(61, 'Samoa', 'Last Sunday of September', NULL, 'First Sunday of April', NULL),
(62, 'San Marino', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(63, 'Serbia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(64, 'Slovakia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(65, 'Slovenia', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(66, 'Spain', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(67, 'Sweden', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(68, 'Switzerland', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(69, 'Syria', 'Last Friday of March', NULL, 'Last Friday of October', NULL),
(70, 'Turkey', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(71, 'Ukraine', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL),
(72, 'United Kingdom', 'last Sunday of March', '01:00', 'last Sunday of October', '01:00'),
(73, 'United States', 'Second Sunday of March', NULL, 'First Sunday of November', NULL),
(74, 'Western Sahara', 'Last Sunday of March', NULL, 'Last Sunday of October', NULL);");
	}

}