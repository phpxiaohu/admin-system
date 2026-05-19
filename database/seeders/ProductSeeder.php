<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空现有数据
        Product::truncate();

        $products = [
            // 手机类
            ['name' => 'iPhone 15 Pro', 'description' => '苹果最新旗舰手机，A17 Pro芯片', 'price' => 8999.00, 'stock' => 150],
            ['name' => 'iPhone 15', 'description' => '苹果手机，A16芯片，灵动岛设计', 'price' => 5999.00, 'stock' => 200],
            ['name' => 'Samsung Galaxy S24', 'description' => '三星旗舰手机，AI功能强大', 'price' => 6999.00, 'stock' => 120],
            ['name' => 'Xiaomi 14', 'description' => '小米旗舰，徕卡影像系统', 'price' => 3999.00, 'stock' => 300],
            ['name' => 'Huawei Mate 60', 'description' => '华为旗舰，卫星通信功能', 'price' => 6999.00, 'stock' => 100],
            ['name' => 'OPPO Find X7', 'description' => 'OPPO旗舰，哈苏影像', 'price' => 4999.00, 'stock' => 180],
            ['name' => 'vivo X100', 'description' => 'vivo旗舰，蔡司光学镜头', 'price' => 4599.00, 'stock' => 160],
            ['name' => 'OnePlus 12', 'description' => '一加旗舰，性能强劲', 'price' => 4299.00, 'stock' => 140],
            ['name' => 'Realme GT5', 'description' => '真我旗舰，性价比高', 'price' => 2999.00, 'stock' => 250],
            ['name' => 'Honor Magic 6', 'description' => '荣耀旗舰，护眼屏幕', 'price' => 4699.00, 'stock' => 130],

            // 笔记本电脑
            ['name' => 'MacBook Pro 16"', 'description' => '苹果笔记本，M3 Max芯片', 'price' => 19999.00, 'stock' => 80],
            ['name' => 'MacBook Air 13"', 'description' => '苹果轻薄本，M3芯片', 'price' => 8999.00, 'stock' => 150],
            ['name' => 'Dell XPS 15', 'description' => '戴尔高端笔记本，4K屏幕', 'price' => 12999.00, 'stock' => 60],
            ['name' => 'Lenovo ThinkPad X1', 'description' => '联想商务本，轻薄便携', 'price' => 10999.00, 'stock' => 90],
            ['name' => 'HP Spectre x360', 'description' => '惠普翻转本，触控屏幕', 'price' => 9999.00, 'stock' => 70],
            ['name' => 'ASUS ROG Zephyrus', 'description' => '华硕游戏本，RTX显卡', 'price' => 11999.00, 'stock' => 100],
            ['name' => 'Acer Swift 3', 'description' => '宏碁轻薄本，性价比高', 'price' => 4999.00, 'stock' => 200],
            ['name' => 'Microsoft Surface Laptop', 'description' => '微软笔记本，触屏设计', 'price' => 8999.00, 'stock' => 110],
            ['name' => 'Razer Blade 15', 'description' => '雷蛇游戏本，RGB键盘', 'price' => 13999.00, 'stock' => 50],
            ['name' => 'Huawei MateBook X', 'description' => '华为笔记本，全面屏设计', 'price' => 7999.00, 'stock' => 120],

            // 平板电脑
            ['name' => 'iPad Pro 12.9"', 'description' => '苹果平板，M2芯片', 'price' => 8999.00, 'stock' => 100],
            ['name' => 'iPad Air', 'description' => '苹果平板，M1芯片', 'price' => 4799.00, 'stock' => 180],
            ['name' => 'Samsung Galaxy Tab S9', 'description' => '三星平板，AMOLED屏幕', 'price' => 5999.00, 'stock' => 90],
            ['name' => 'Xiaomi Pad 6', 'description' => '小米平板，高刷新率', 'price' => 1999.00, 'stock' => 250],
            ['name' => 'Huawei MatePad Pro', 'description' => '华为平板，鸿蒙系统', 'price' => 3999.00, 'stock' => 130],
            ['name' => 'Lenovo Tab P12', 'description' => '联想平板，大屏办公', 'price' => 2499.00, 'stock' => 160],
            ['name' => 'Microsoft Surface Pro', 'description' => '微软二合一平板', 'price' => 7999.00, 'stock' => 80],
            ['name' => 'OPPO Pad 2', 'description' => 'OPPO平板，影音娱乐', 'price' => 2299.00, 'stock' => 140],
            ['name' => 'Honor Pad V8', 'description' => '荣耀平板，学习办公', 'price' => 1799.00, 'stock' => 200],
            ['name' => 'Amazon Fire HD', 'description' => '亚马逊平板，阅读利器', 'price' => 999.00, 'stock' => 300],

            // 智能手表
            ['name' => 'Apple Watch Ultra 2', 'description' => '苹果手表，户外运动', 'price' => 6499.00, 'stock' => 120],
            ['name' => 'Apple Watch Series 9', 'description' => '苹果手表，健康监测', 'price' => 2999.00, 'stock' => 200],
            ['name' => 'Samsung Galaxy Watch 6', 'description' => '三星手表，旋转表圈', 'price' => 2299.00, 'stock' => 150],
            ['name' => 'Huawei Watch GT 4', 'description' => '华为手表，长续航', 'price' => 1688.00, 'stock' => 180],
            ['name' => 'Garmin Fenix 7', 'description' => '佳明手表，专业运动', 'price' => 5980.00, 'stock' => 80],
            ['name' => 'Amazfit GTR 4', 'description' => '华米手表，性价比高', 'price' => 999.00, 'stock' => 250],
            ['name' => 'Fitbit Sense 2', 'description' => ' Fitbit手表，压力监测', 'price' => 1999.00, 'stock' => 100],
            ['name' => 'Xiaomi Watch S3', 'description' => '小米手表，可换表圈', 'price' => 799.00, 'stock' => 300],
            ['name' => 'OPPO Watch 3', 'description' => 'OPPO手表，eSIM通话', 'price' => 1499.00, 'stock' => 140],
            ['name' => 'Honor Watch 4', 'description' => '荣耀手表，健康管理', 'price' => 1299.00, 'stock' => 160],

            // 耳机音响
            ['name' => 'AirPods Pro 2', 'description' => '苹果耳机，主动降噪', 'price' => 1899.00, 'stock' => 250],
            ['name' => 'Sony WH-1000XM5', 'description' => '索尼耳机，顶级降噪', 'price' => 2499.00, 'stock' => 150],
            ['name' => 'Bose QC45', 'description' => 'Bose耳机，舒适佩戴', 'price' => 2299.00, 'stock' => 120],
            ['name' => 'Sennheiser Momentum 4', 'description' => '森海塞尔耳机，HiFi音质', 'price' => 2799.00, 'stock' => 80],
            ['name' => 'JBL Flip 6', 'description' => 'JBL音箱，便携蓝牙', 'price' => 799.00, 'stock' => 300],
            ['name' => 'Marshall Emberton', 'description' => '马歇尔音箱，摇滚风格', 'price' => 1299.00, 'stock' => 100],
            ['name' => 'Beats Studio Pro', 'description' => 'Beats耳机，低音强劲', 'price' => 2699.00, 'stock' => 130],
            ['name' => 'Xiaomi Buds 4 Pro', 'description' => '小米耳机，空间音频', 'price' => 999.00, 'stock' => 200],
            ['name' => 'Huawei FreeBuds 5', 'description' => '华为耳机，半入耳设计', 'price' => 1199.00, 'stock' => 180],
            ['name' => 'Anker Soundcore', 'description' => '安克音箱，超长续航', 'price' => 599.00, 'stock' => 350],

            // 相机摄影
            ['name' => 'Canon EOS R6 II', 'description' => '佳能微单，全画幅', 'price' => 15999.00, 'stock' => 50],
            ['name' => 'Sony A7 IV', 'description' => '索尼微单，视频强项', 'price' => 14999.00, 'stock' => 60],
            ['name' => 'Nikon Z8', 'description' => '尼康微单，高像素', 'price' => 25999.00, 'stock' => 30],
            ['name' => 'Fujifilm X-T5', 'description' => '富士微单，复古外观', 'price' => 11999.00, 'stock' => 70],
            ['name' => 'GoPro Hero 12', 'description' => '运动相机，防抖出色', 'price' => 3498.00, 'stock' => 150],
            ['name' => 'DJI Osmo Pocket 3', 'description' => '大疆云台相机，Vlog神器', 'price' => 3499.00, 'stock' => 120],
            ['name' => 'Insta360 X3', 'description' => '全景相机，360度拍摄', 'price' => 2998.00, 'stock' => 100],
            ['name' => 'Polaroid Now+', 'description' => '宝丽来相机，即时成像', 'price' => 1299.00, 'stock' => 80],
            ['name' => 'Leica Q3', 'description' => '徕卡相机，德味十足', 'price' => 39800.00, 'stock' => 20],
            ['name' => 'Panasonic Lumix S5', 'description' => '松下微单，视频利器', 'price' => 9998.00, 'stock' => 90],

            // 智能家居
            ['name' => 'Xiaomi Smart Speaker', 'description' => '小爱音箱，语音助手', 'price' => 299.00, 'stock' => 500],
            ['name' => 'Amazon Echo Dot', 'description' => '亚马逊音箱，Alexa助手', 'price' => 399.00, 'stock' => 400],
            ['name' => 'Google Nest Hub', 'description' => '谷歌智能屏，家居控制', 'price' => 699.00, 'stock' => 200],
            ['name' => 'Philips Hue Bulb', 'description' => '飞利浦智能灯泡，多彩调光', 'price' => 199.00, 'stock' => 600],
            ['name' => 'TP-Link Smart Plug', 'description' => '智能插座，远程控制', 'price' => 79.00, 'stock' => 800],
            ['name' => 'Ring Video Doorbell', 'description' => '智能门铃，可视对讲', 'price' => 799.00, 'stock' => 150],
            ['name' => 'Nest Thermostat', 'description' => '智能温控器，节能省电', 'price' => 1299.00, 'stock' => 100],
            ['name' => 'Roborock S8', 'description' => '石头扫地机器人，自动集尘', 'price' => 3999.00, 'stock' => 120],
            ['name' => 'Dyson Purifier', 'description' => '戴森空气净化器，除甲醛', 'price' => 4990.00, 'stock' => 80],
            ['name' => 'Ecovacs Deebot', 'description' => '科沃斯扫地机，智能导航', 'price' => 2999.00, 'stock' => 140],

            // 更多商品 - 补充到100条
            ['name' => 'Xiaomi Router AX9000', 'description' => '小米路由器，WiFi6三频', 'price' => 999.00, 'stock' => 200],
            ['name' => 'ASUS RT-AX86U', 'description' => '华硕路由器，游戏加速', 'price' => 1299.00, 'stock' => 150],
            ['name' => 'Logitech MX Master 3', 'description' => '罗技鼠标，办公神器', 'price' => 699.00, 'stock' => 300],
            ['name' => 'Keychron K8', 'description' => '机械键盘，无线蓝牙', 'price' => 599.00, 'stock' => 250],
            ['name' => 'Samsung T7 SSD', 'description' => '三星移动硬盘，1TB', 'price' => 799.00, 'stock' => 180],
            ['name' => 'SanDisk Extreme Pro', 'description' => '闪迪存储卡，高速读写', 'price' => 299.00, 'stock' => 400],
            ['name' => 'Anker PowerCore', 'description' => '安克充电宝，20000mAh', 'price' => 199.00, 'stock' => 500],
            ['name' => 'Baseus GaN Charger', 'description' => '倍思充电器，65W快充', 'price' => 149.00, 'stock' => 600],
            ['name' => 'UGREEN USB-C Hub', 'description' => '绿联扩展坞，多接口', 'price' => 259.00, 'stock' => 350],
            ['name' => 'BenQ Monitor SW270C', 'description' => '明基显示器，专业修图', 'price' => 4999.00, 'stock' => 60],
            ['name' => 'LG UltraGear 27GN950', 'description' => 'LG显示器，4K 144Hz', 'price' => 5999.00, 'stock' => 70],
            ['name' => 'SteelSeries Arctis 7', 'description' => '赛睿耳机，游戏专用', 'price' => 1299.00, 'stock' => 120],
            ['name' => 'Razer DeathAdder V3', 'description' => '雷蛇鼠标，电竞级', 'price' => 499.00, 'stock' => 280],
            ['name' => 'Corsair K95 RGB', 'description' => '海盗船键盘，机械轴', 'price' => 1499.00, 'stock' => 90],
            ['name' => 'Elgato Stream Deck', 'description' => '直播控制台，快捷键', 'price' => 1199.00, 'stock' => 100],
            ['name' => 'Blue Yeti Microphone', 'description' => '蓝麦克风，USB录音', 'price' => 899.00, 'stock' => 130],
            ['name' => 'Ring Light 18inch', 'description' => '补光灯，直播拍照', 'price' => 299.00, 'stock' => 400],
            ['name' => 'Manfrotto Tripod', 'description' => '曼富图三脚架，专业摄影', 'price' => 1599.00, 'stock' => 80],
            ['name' => 'DJI Mini 3 Pro', 'description' => '大疆无人机，轻便航拍', 'price' => 4788.00, 'stock' => 90],
            ['name' => 'Nintendo Switch OLED', 'description' => '任天堂游戏机，OLED屏幕', 'price' => 2399.00, 'stock' => 200],
            ['name' => 'PlayStation 5', 'description' => '索尼游戏机，次世代', 'price' => 3899.00, 'stock' => 100],
            ['name' => 'Xbox Series X', 'description' => '微软游戏机，4K游戏', 'price' => 3699.00, 'stock' => 110],
            ['name' => 'Steam Deck', 'description' => 'Valve掌机，PC游戏', 'price' => 3499.00, 'stock' => 130],
            ['name' => 'Meta Quest 3', 'description' => 'VR头显，虚拟现实', 'price' => 3999.00, 'stock' => 80],
            ['name' => 'Apple TV 4K', 'description' => '苹果电视盒子，流媒体', 'price' => 1499.00, 'stock' => 150],
            ['name' => 'Sonos One Speaker', 'description' => 'Sonos音箱，智能音响', 'price' => 1699.00, 'stock' => 120],
            ['name' => 'Nespresso Coffee Machine', 'description' => '奈斯派索咖啡机，胶囊式', 'price' => 1299.00, 'stock' => 100],
            ['name' => 'Dyson Hair Dryer', 'description' => '戴森吹风机，速干护发', 'price' => 2990.00, 'stock' => 90],
            ['name' => 'Philips Air Fryer', 'description' => '飞利浦空气炸锅，无油烹饪', 'price' => 899.00, 'stock' => 180],
            ['name' => 'Instant Pot Duo', 'description' => '电压力锅，多功能烹饪', 'price' => 699.00, 'stock' => 200],
        ];

        // 随机打乱顺序并取100条
        shuffle($products);
        $products = array_slice($products, 0, 100);

        // 插入数据
        foreach ($products as $index => $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => "https://picsum.photos/seed/product{$index}/400/400",
                'is_active' => true,
            ]);
        }

        $this->command->info('成功创建100条商品数据！');
    }
}
