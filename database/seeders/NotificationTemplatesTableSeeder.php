<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplatesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notification_templates')->updateOrInsert(
            ['id' => 7],
            [
                'act' => 'PASS_RESET_CODE',
                'name' => 'Password - Reset - Code',
                'subject' => 'Password Reset',
                'email_body' => '<div style="font-family: Montserrat, sans-serif;">We have received a request to reset the password for your account on&nbsp;<span style="font-weight: bolder;">{{time}} .<br></span></div><div style="font-family: Montserrat, sans-serif;">Requested From IP:&nbsp;<span style="font-weight: bolder;">{{ip}}</span>&nbsp;using&nbsp;<span style="font-weight: bolder;">{{browser}}</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{operating_system}}&nbsp;</span>.</div><div style="font-family: Montserrat, sans-serif;"><br></div><br style="font-family: Montserrat, sans-serif;"><div style="font-family: Montserrat, sans-serif;"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size="6"><span style="font-weight: bolder;">{{code}}</span></font></div><div><br></div></div><div style="font-family: Montserrat, sans-serif;"><br></div><div style="font-family: Montserrat, sans-serif;"><font size="4" color="#CC0000">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size="4" color="#CC0000"><br></font></div>',
                'sms_body' => 'Your account recovery code is: {{code}}',
                'shortcodes' => '{"code":"Verification code for password reset","ip":"IP address of the user","browser":"Browser of the user","operating_system":"Operating system of the user","time":"Time of the request"}',
                'email_status' => 1,
                'email_sent_from_name' => null,
                'email_sent_from_address' => null,
                'sms_status' => 0,
                'sms_sent_from' => null,
                'created_at' => '2021-11-03 12:00:00',
                'updated_at' => '2022-03-20 20:47:05',
            ]
        );

        DB::table('notification_templates')->updateOrInsert(
            ['id' => 8],
            [
                'act' => 'PASS_RESET_DONE',
                'name' => 'Password - Reset - Confirmation',
                'subject' => 'You have reset your password',
                'email_body' => '<p style="font-family: Montserrat, sans-serif;">You have successfully reset your password.</p><p style="font-family: Montserrat, sans-serif;">You changed from&nbsp; IP:&nbsp;<span style="font-weight: bolder;">{{ip}}</span>&nbsp;using&nbsp;<span style="font-weight: bolder;">{{browser}}</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{time}}</span></p><p style="font-family: Montserrat, sans-serif;"><span style="font-weight: bolder;"><br></span></p><p style="font-family: Montserrat, sans-serif;"><span style="font-weight: bolder;"><font color="#ff0000">If you did not change that, please contact us as soon as possible.</font></span></p>',
                'sms_body' => 'Your password has been changed successfully',
                'shortcodes' => '{"ip":"IP address of the user","browser":"Browser of the user","operating_system":"Operating system of the user","time":"Time of the request"}',
                'email_status' => 1,
                'email_sent_from_name' => null,
                'email_sent_from_address' => null,
                'sms_status' => 1,
                'sms_sent_from' => null,
                'created_at' => '2021-11-03 12:00:00',
                'updated_at' => '2022-04-05 03:46:35',
            ]
        );
    }
}