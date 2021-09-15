<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Config
 * @license    LGPL
 * @filesource
 */


/**
 * Countries
 */
$countries = array
(
	'ac' => 'Ascension Island',
	'ad' => 'Andorra',
	'ae' => 'United Arab Emirates',
	'af' => 'Afghanistan',
	'ag' => 'Antigua and Barbuda',
	'ai' => 'Anguilla',
	'al' => 'Albania',
	'am' => 'Armenia',
	'an' => 'Netherlands Antilles',
	'ao' => 'Angola',
	'aq' => 'Antarctica',
	'ar' => 'Argentina',
	'as' => 'American Samoa',
	'at' => 'Austria',
	'au' => 'Australia',
	'aw' => 'Aruba',
	'az' => 'Azerbaijan',
	'ax' => 'Aland Islands',
	'ba' => 'Bosnia and Herzegovina',
	'bb' => 'Barbados',
	'bd' => 'Bangladesh',
	'be' => 'Belgium',
	'bf' => 'Burkina Faso',
	'bg' => 'Bulgaria',
	'bh' => 'Bahrain',
	'bi' => 'Burundi',
	'bj' => 'Benin',
	'bm' => 'Bermuda',
	'bn' => 'Brunei Darussalam',
	'bo' => 'Bolivia',
	'br' => 'Brazil',
	'bs' => 'Bahamas',
	'bt' => 'Bhutan',
	'bv' => 'Bouvet Island',
	'bw' => 'Botswana',
	'by' => 'Belarus',
	'bz' => 'Belize',
	'ca' => 'Canada',
	'cc' => 'Cocos (Keeling) Islands',
	'cd' => 'Congo, The Democratic Republic of the',
	'cf' => 'Central African Republic',
	'cg' => 'Congo, Republic of',
	'ch' => 'Switzerland',
	'ci' => 'Cote d\'Ivoire',
	'ck' => 'Cook Islands',
	'cl' => 'Chile',
	'cm' => 'Cameroon',
	'cn' => 'China',
	'co' => 'Colombia',
	'cr' => 'Costa Rica',
	'cs' => 'Serbia and Montenegro',
	'cu' => 'Cuba',
	'cv' => 'Cape Verde',
	'cx' => 'Christmas Island',
	'cy' => 'Cyprus',
	'cz' => 'Czech Republic',
	'de' => 'Germany',
	'dj' => 'Djibouti',
	'dk' => 'Denmark',
	'dm' => 'Dominica',
	'do' => 'Dominican Republic',
	'dz' => 'Algeria',
	'ec' => 'Ecuador',
	'ee' => 'Estonia',
	'eg' => 'Egypt',
	'eh' => 'Western Sahara',
	'er' => 'Eritrea',
	'es' => 'Spain',
	'et' => 'Ethiopia',
	'eu' => 'European Union',
	'fi' => 'Finland',
	'fj' => 'Fiji',
	'fk' => 'Falkland Islands (Malvinas)',
	'fm' => 'Micronesia',
	'fo' => 'Faroe Islands',
	'fr' => 'France',
	'ga' => 'Gabon',
	'gd' => 'Grenada',
	'ge' => 'Georgia',
	'gf' => 'French Guiana',
	'gg' => 'Guernsey',
	'gh' => 'Ghana',
	'gi' => 'Gibraltar',
	'gl' => 'Greenland',
	'gm' => 'Gambia',
	'gn' => 'Guinea',
	'gp' => 'Guadeloupe',
	'gq' => 'Equatorial Guinea',
	'gr' => 'Greece',
	'gs' => 'South Georgia and the South Sandwich Islands',
	'gt' => 'Guatemala',
	'gu' => 'Guam',
	'gw' => 'Guinea-Bissau',
	'gy' => 'Guyana',
	'hk' => 'Hong Kong',
	'hm' => 'Heard and McDonald Islands',
	'hn' => 'Honduras',
	'hr' => 'Croatia/Hrvatska',
	'ht' => 'Haiti',
	'hu' => 'Hungary',
	'id' => 'Indonesia',
	'ie' => 'Ireland',
	'il' => 'Israel',
	'im' => 'Isle of Man',
	'in' => 'India',
	'io' => 'British Indian Ocean Territory',
	'iq' => 'Iraq',
	'ir' => 'Iran',
	'is' => 'Iceland',
	'it' => 'Italy',
	'je' => 'Jersey',
	'jm' => 'Jamaica',
	'jo' => 'Jordan',
	'jp' => 'Japan',
	'ke' => 'Kenya',
	'kg' => 'Kyrgyzstan',
	'kh' => 'Cambodia',
	'ki' => 'Kiribati',
	'km' => 'Comoros',
	'kn' => 'Saint Kitts and Nevis',
	'kp' => 'North Korea',
	'kr' => 'South Korea',
	'kw' => 'Kuwait',
	'ky' => 'Cayman Islands',
	'kz' => 'Kazakhstan',
	'la' => 'Lao',
	'lb' => 'Lebanon',
	'lc' => 'Saint Lucia',
	'li' => 'Liechtenstein',
	'lk' => 'Sri Lanka',
	'lr' => 'Liberia',
	'ls' => 'Lesotho',
	'lt' => 'Lithuania',
	'lu' => 'Luxembourg',
	'lv' => 'Latvia',
	'ly' => 'Libyan Arab Jamahiriya',
	'ma' => 'Morocco',
	'mc' => 'Monaco',
	'md' => 'Moldova, Republic of',
	'mg' => 'Madagascar',
	'mh' => 'Marshall Islands',
	'mk' => 'Macedonia',
	'ml' => 'Mali',
	'mm' => 'Myanmar',
	'mn' => 'Mongolia',
	'mo' => 'Macau',
	'mp' => 'Northern Mariana Islands',
	'mq' => 'Martinique',
	'mr' => 'Mauritania',
	'ms' => 'Montserrat',
	'mt' => 'Malta',
	'mu' => 'Mauritius',
	'mv' => 'Maldives',
	'mw' => 'Malawi',
	'mx' => 'Mexico',
	'my' => 'Malaysia',
	'mz' => 'Mozambique',
	'na' => 'Namibia',
	'nc' => 'New Caledonia',
	'ne' => 'Niger',
	'nf' => 'Norfolk Island',
	'ng' => 'Nigeria',
	'ni' => 'Nicaragua',
	'nl' => 'Netherlands',
	'no' => 'Norway',
	'np' => 'Nepal',
	'nr' => 'Nauru',
	'nu' => 'Niue',
	'nz' => 'New Zealand',
	'om' => 'Oman',
	'pa' => 'Panama',
	'pe' => 'Peru',
	'pf' => 'French Polynesia',
	'pg' => 'Papua New Guinea',
	'ph' => 'Philippines',
	'pk' => 'Pakistan',
	'pl' => 'Poland',
	'pm' => 'Saint Pierre and Miquelon',
	'pn' => 'Pitcairn Island',
	'pr' => 'Puerto Rico',
	'ps' => 'Palestinian Territories',
	'pt' => 'Portugal',
	'pw' => 'Palau',
	'py' => 'Paraguay',
	'qa' => 'Qatar',
	're' => 'Reunion Island',
	'ro' => 'Romania',
	'ru' => 'Russian Federation',
	'rw' => 'Rwanda',
	'sa' => 'Saudi Arabia',
	'sb' => 'Solomon Islands',
	'sc' => 'Seychelles',
	'sd' => 'Sudan',
	'se' => 'Sweden',
	'sg' => 'Singapore',
	'sh' => 'Saint Helena',
	'si' => 'Slovenia',
	'sj' => 'Svalbard and Jan Mayen Islands',
	'sk' => 'Slovak Republic',
	'sl' => 'Sierra Leone',
	'sm' => 'San Marino',
	'sn' => 'Senegal',
	'so' => 'Somalia',
	'sr' => 'Suriname',
	'st' => 'Sao Tome and Principe',
	'sv' => 'El Salvador',
	'sy' => 'Syrian Arab Republic',
	'sz' => 'Swaziland',
	'tc' => 'Turks and Caicos Islands',
	'td' => 'Chad',
	'tf' => 'French Southern Territories',
	'tg' => 'Togo',
	'th' => 'Thailand',
	'tj' => 'Tajikistan',
	'tk' => 'Tokelau',
	'tl' => 'Timor-Leste',
	'tm' => 'Turkmenistan',
	'tn' => 'Tunisia',
	'to' => 'Tonga',
	'tp' => 'East Timor',
	'tr' => 'Turkey',
	'tt' => 'Trinidad and Tobago',
	'tv' => 'Tuvalu',
	'tw' => 'Taiwan',
	'tz' => 'Tanzania',
	'ua' => 'Ukraine',
	'ug' => 'Uganda',
	'uk' => 'United Kingdom',
	'um' => 'United States Minor Outlying Islands',
	'us' => 'United States',
	'uy' => 'Uruguay',
	'uz' => 'Uzbekistan',
	'va' => 'Holy See (Vatican City State)',
	'vc' => 'Saint Vincent and the Grenadines',
	've' => 'Venezuela',
	'vg' => 'Virgin Islands, British',
	'vi' => 'Virgin Islands, U.S.',
	'vn' => 'Vietnam',
	'vu' => 'Vanuatu',
	'wf' => 'Wallis and Futuna Islands',
	'ws' => 'Samoa',
	'ye' => 'Yemen',
	'yt' => 'Mayotte',
	'yu' => 'Yugoslavia',
	'za' => 'South Africa',
	'zm' => 'Zambia',
	'zw' => 'Zimbabwe',
);

?>
