<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Setting::site() returns correct fallback values when DB is empty.
     */
    public function test_site_settings_fallbacks(): void
    {
        $this->assertEquals('Phòng Khám Đa Khoa Cần Thơ', Setting::site('clinic_name'));
        $this->assertEquals('Đa Khoa Cần Thơ', Setting::site('clinic_short_name'));
        $this->assertEquals('Số 57 Hùng Vương, P. Ninh Kiều, TP. Cần Thơ', Setting::site('address'));
        $this->assertEquals('0966.332.352', Setting::site('hotline'));
        $this->assertEquals('info@dakhoagiaphuoc.vn', Setting::site('email'));
        $this->assertEquals('https://maps.app.goo.gl/DtvjNfmhPru9z1HD9', Setting::site('google_maps_url'));
        $this->assertEquals('07:30 - 20:00 (Tất cả các ngày trong tuần, kể cả Lễ)', Setting::site('working_hours'));
    }

    /**
     * Test Setting::site() returns overridden values from the database.
     */
    public function test_site_settings_db_override(): void
    {
        Setting::set('site_settings', [
            'clinic_name' => 'Custom Clinic Name',
            'clinic_short_name' => 'Custom Short Name',
            'address' => 'Custom Address',
            'hotline' => '0988.888.888',
        ]);

        $this->assertEquals('Custom Clinic Name', Setting::site('clinic_name'));
        $this->assertEquals('Custom Short Name', Setting::site('clinic_short_name'));
        $this->assertEquals('Custom Address', Setting::site('address'));
        $this->assertEquals('0988.888.888', Setting::site('hotline'));
        
        // Unmodified settings should still return fallbacks
        $this->assertEquals('info@dakhoagiaphuoc.vn', Setting::site('email'));
    }

    /**
     * Test that contact page displays the configured address and telephone.
     */
    public function test_contact_page_displays_settings(): void
    {
        Setting::set('site_settings', [
            'clinic_name' => 'Gia Phước Test Clinic',
            'address' => '123 Test Street, Ninh Kiều, Cần Thơ',
            'hotline' => '0999.999.999',
            'email' => 'test@dakhoagiaphuoc.vn',
        ]);

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Gia Phước Test Clinic');
        $response->assertSee('123 Test Street, Ninh Kiều, Cần Thơ');
        $response->assertSee('0999.999.999');
        $response->assertSee('test@dakhoagiaphuoc.vn');
    }

    /**
     * Test that homepage footer loads the site settings.
     */
    public function test_footer_displays_settings(): void
    {
        Setting::set('site_settings', [
            'clinic_name' => 'Gia Phước Test Footer Clinic',
            'address' => '456 Test Street, Ninh Kiều, Cần Thơ',
            'hotline' => '0911.111.111',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Gia Phước Test Footer Clinic');
        $response->assertSee('456 Test Street, Ninh Kiều, Cần Thơ');
        $response->assertSee('0911.111.111');
    }

    /**
     * Test that JSON-LD structured data is updated with dynamic settings.
     */
    public function test_json_ld_schema_contains_settings(): void
    {
        Setting::set('site_settings', [
            'clinic_name' => 'Gia Phước Schema Clinic',
            'address' => '789 Schema Road, Ninh Kiều, Cần Thơ',
            'hotline' => '0922.222.222',
            'email' => 'schema@dakhoagiaphuoc.vn',
            'latitude' => '10.043858',
            'longitude' => '105.778917',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        
        // Search inside raw response to find JSON-LD components
        $html = $response->getContent();
        
        $this->assertStringContainsString('"@type":"LocalBusiness"', $html);
        $this->assertStringContainsString('"name":"Gia Ph\u01b0\u1edbc Schema Clinic"', $html); // Unicode escape for Gia Phước Schema Clinic
        $this->assertStringContainsString('"telephone":"+84922222222"', $html); // Cleaned E164 number
        $this->assertStringContainsString('"email":"schema@dakhoagiaphuoc.vn"', $html);
        $this->assertStringContainsString('"streetAddress":"789 Schema Road"', $html);
        $this->assertStringContainsString('"addressLocality":"Ninh Ki\u1ec1u"', $html);
        $this->assertStringContainsString('"addressRegion":"C\u1ea7n Th\u01a1"', $html);
    }
}
