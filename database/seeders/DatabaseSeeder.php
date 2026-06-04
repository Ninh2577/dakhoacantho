<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User for Filament
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@dakhoacantho.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Admin VN',
            'email' => 'admin@dakhoacantho.vn',
            'password' => bcrypt('password123@#'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'IGF Admin',
            'email' => 'igf@dakhoacantho.vn',
            'password' => bcrypt('igf'),
            'role' => 'admin',
        ]);

        // User::factory(10)->create();

        // Create categories matching the mockups
        $catNamKhoa = \App\Models\Category::create([
            'name' => 'Nam Khoa',
            'slug' => 'nam-khoa',
            'description' => 'Chuyên mục tư vấn và điều trị các bệnh lý nam khoa.',
        ]);

        $catPhuKhoa = \App\Models\Category::create([
            'name' => 'Phụ Khoa',
            'slug' => 'phu-khoa',
            'description' => 'Chăm sóc sức khỏe phụ nữ toàn diện và tầm soát bệnh lý phụ khoa định kỳ.',
        ]);

        $catBenhXaHoi = \App\Models\Category::create([
            'name' => 'Bệnh Xã Hội',
            'slug' => 'benh-xa-hoi',
            'description' => 'Xét nghiệm bảo mật tuyệt đối, điều trị dứt điểm sùi mào gà, lậu, giang mai.',
        ]);

        $catHauMon = \App\Models\Category::create([
            'name' => 'Hậu Môn - Trực Tràng',
            'slug' => 'hau-mon-truc-trang',
            'description' => 'Chẩn đoán trĩ nội, trĩ ngoại và rò hậu môn bằng phương pháp PPH, HCPT không đau.',
        ]);

        $catNgoaiKhoa = \App\Models\Category::create([
            'name' => 'Ngoại Khoa',
            'slug' => 'ngoai-khoa',
            'description' => 'Điều trị các bệnh ngoại khoa tiểu phẫu.',
        ]);

        // Create a test article under Nam Khoa
        \App\Models\Article::create([
            'category_id' => $catNamKhoa->id,
            'title' => 'Bệnh yếu sinh lý ở nam giới và phương pháp điều trị hiệu quả',
            'slug' => 'benh-yeu-sinh-ly-nam-gioi',
            'content' => '<h2>Yếu sinh lý là gì?</h2><p>Yếu sinh lý là tình trạng suy giảm khả năng tình dục ở nam giới, gây ảnh hưởng lớn đến tâm lý và hạnh phúc gia đình.</p><h2>Nguyên nhân gây bệnh</h2><ul><li>Căng thẳng, stress kéo dài.</li><li>Lạm dụng chất kích thích.</li><li>Mắc các bệnh lý mãn tính.</li></ul><h2>Cách điều trị hiệu quả</h2><p>Bệnh nhân cần đến trực tiếp phòng khám chuyên khoa để được bác sĩ thăm khám và đưa ra phác đồ điều trị phù hợp nhất.</p>',
            'thumbnail_image' => null,
            'meta_title' => 'Điều trị bệnh yếu sinh lý nam giới hiệu quả | Đa Khoa Cần Thơ',
            'meta_description' => 'Yếu sinh lý nam giới là gì? Nguyên nhân, triệu chứng và phương pháp điều trị yếu sinh lý hiệu quả tại Phòng khám Đa Khoa Cần Thơ.',
            'is_published' => true,
        ]);

        // Create a test article under Phu Khoa
        \App\Models\Article::create([
            'category_id' => $catPhuKhoa->id,
            'title' => 'Viêm nhiễm phụ khoa và những điều chị em phụ nữ cần đặc biệt lưu ý',
            'slug' => 'viem-nhiem-phu-khoa-luu-y',
            'content' => '<h2>Viêm nhiễm phụ khoa là gì?</h2><p>Đây là các tình trạng viêm nhiễm tại cơ quan sinh dục nữ, rất phổ biến ở mọi lứa tuổi.</p><h2>Các biện pháp phòng tránh</h2><ul><li>Vệ sinh cá nhân sạch sẽ hàng ngày.</li><li>Khám phụ khoa định kỳ mỗi 6 tháng.</li></ul>',
            'thumbnail_image' => null,
            'meta_title' => 'Viêm nhiễm phụ khoa và những điều lưu ý | Đa Khoa Cần Thơ',
            'meta_description' => 'Tìm hiểu về bệnh lý viêm nhiễm phụ khoa ở phụ nữ, nguyên nhân và biện pháp điều trị an toàn tại Đa Khoa Cần Thơ.',
            'is_published' => true,
        ]);

        // Create a test article under Benh Xa Hoi
        \App\Models\Article::create([
            'category_id' => $catBenhXaHoi->id,
            'title' => 'Xét nghiệm sùi mào gà chính xác, an toàn và bảo mật tại Cần Thơ',
            'slug' => 'xet-nghiem-sui-mao-ga-can-tho',
            'content' => '<h2>Tầm quan trọng của xét nghiệm sớm</h2><p>Sùi mào gà cần được xét nghiệm phát hiện sớm để tránh các biến chứng nguy hại cho sức khỏe sinh sản.</p>',
            'thumbnail_image' => null,
            'meta_title' => 'Xét nghiệm sùi mào gà bảo mật tại Cần Thơ | Đa Khoa Cần Thơ',
            'meta_description' => 'Dịch vụ xét nghiệm bệnh sùi mào gà nhanh chóng, chính xác và bảo mật tuyệt đối thông tin bệnh nhân.',
            'is_published' => true,
        ]);

        // Create a test article under Hau Mon
        \App\Models\Article::create([
            'category_id' => $catHauMon->id,
            'title' => 'Điều trị bệnh trĩ hiệu quả bằng công nghệ hiện đại PPH và HCPT',
            'slug' => 'dieu-tri-benh-tri-pph-hcpt',
            'content' => '<h2>Phương pháp PPH & HCPT là gì?</h2><p>Đây là hai kỹ thuật xâm lấn tối thiểu tiên tiến nhất giúp điều trị các búi trĩ hiệu quả, phục hồi nhanh, không đau đớn.</p>',
            'thumbnail_image' => null,
            'meta_title' => 'Điều trị bệnh trĩ bằng phương pháp PPH/HCPT | Đa Khoa Cần Thơ',
            'meta_description' => 'Tìm hiểu phương pháp cắt trĩ PPH và HCPT không đau, ít chảy máu, hồi phục nhanh tại Đa Khoa Cần Thơ.',
            'is_published' => true,
        ]);
    }
}
