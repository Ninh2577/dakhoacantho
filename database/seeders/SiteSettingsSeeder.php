<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('site_settings', [
            'clinic_name' => 'Phòng Khám Đa Khoa Cần Thơ',
            'clinic_short_name' => 'Đa Khoa Cần Thơ',
            'address' => 'Số 57 Hùng Vương, P. Ninh Kiều, TP. Cần Thơ',
            'hotline' => '0966.332.352',
            'email' => 'info@dakhoagiaphuoc.vn',
            'google_maps_url' => 'https://maps.app.goo.gl/DtvjNfmhPru9z1HD9',
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31429.579020087935!2d105.7704082!3d10.0418118!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a088032bc0311b%3A0x4da06f04ef4663c2!2zxJBhIEtob2EgR2lhIFBoxrDhu5tj!5e0!3m2!1sen!2s!4v1782102895910!5m2!1sen!2s',
            'latitude' => '10.043858',
            'longitude' => '105.778917',
            'facebook_url' => 'https://www.facebook.com/pkdkgiaphuoc',
            'zalo_url' => 'https://zalo.me/0966332352',
            'youtube_url' => 'https://www.youtube.com/@dakhoagiaphuoc',
            'tiktok_url' => 'https://www.tiktok.com/@dakhoagiaphuoc',
            'booking_url' => 'https://app.dakhoacantho.com/lien-he',
            'working_hours' => '07:30 - 20:00 (Tất cả các ngày trong tuần, kể cả Lễ)',
        ]);
    }
}
