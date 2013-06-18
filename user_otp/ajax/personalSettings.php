<?php
<<<<<<< HEAD
/**
 * ownCloud - One Time Password plugin
 *
 * @package user_otp
 * @author Frank Bongrand
 * @copyright 2013 Frank Bongrand fbongrand@free.fr
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 * Displays <a href="http://opensource.org/licenses/AGPL-3.0">GNU AFFERO GENERAL PUBLIC LICENSE</a>
 * @license http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE
 *
 */
=======

/**
* ownCloud - One Time Password plugin
*
* @author Frank Bongrand
* @copyright 2013 Frank Bongrand fbongrand@free.fr
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library. If not, see <http://www.gnu.org/licenses/>.
*
*/

>>>>>>> b0c3d8eb670cc54fcb5668bde845f19fc597c695

$l=OC_L10N::get('settings');

OC_JSON::checkLoggedIn();
OCP\JSON::callCheck();
OC_JSON::checkAppEnabled('user_otp');

// Get data
$mOtp =  new multiotp(OCP\Config::getAppValue(
    'user_otp','EncryptionKey','DefaultCliEncryptionKey')
);
$mOtp->EnableVerboseLog();
$mOtp->SetUsersFolder(
    OCP\Config::getAppValue(
        'user_otp',
        'UsersFolder',
        getcwd()."/apps/user_otp/lib/multiotp/users/"
    )
);

if(
   $_POST &&
   $_POST["otp_action"]==="delete_otp" &&
   $mOtp->CheckUserExists(OCP\User::getUser())
){
    if($mOtp->DeleteUser(OCP\User::getUser())){
        OC_JSON::success(array("data" => array( "message" => $l->t("OTP Changed") )));
    }else{
        OC_JSON::error(array("data" => array( "message" => $l->t("Invalid request") )));
    }
}else if (
    $_POST &&
    $_POST["otp_action"]==="create_otp" &&
    !$mOtp->CheckUserExists(OCP\User::getUser())
){
    // format token seed :
    if($_POST["UserTokenSeed"]===""){
        $UserTokenSeed="";
    }else if (OCP\Config::getAppValue('user_otp','TokenBase32Encode',true)){
        $UserTokenSeed=bin2hex(base32_decode($_POST["UserTokenSeed"]));
    }else{
        $UserTokenSeed=$_POST["UserTokenSeed"];
    }

    $result = $mOtp->CreateUser(
        OCP\User::getUser(),
        (OCP\Config::getAppValue('user_otp','UserPrefixPin','0')?1:0),
        OCP\Config::getAppValue('user_otp','UserAlgorithm','TOTP'),
        $UserTokenSeed,
        $_POST["UserPin"],
        OCP\Config::getAppValue('user_otp','UserTokenNumberOfDigits','6'),
        OCP\Config::getAppValue('user_otp','UserTokenTimeIntervalOrLastEvent','30')
    );
    if($result){
        OC_JSON::success(array("data" => array( "message" => $l->t("OTP Changed") )));
    }else{
        OC_JSON::error(array("data" => array( "message" => $l->t("Invalid request") )));
    }
}else{
    OC_JSON::error(array("data" => array( "message" => $l->t("Invalid request") )));
}
