-- สคริปต์นี้จะถูกรันโดยอัตโนมัติเมื่อ Docker MariaDB เริ่มทำงานครั้งแรก
-- สร้างฐานข้อมูลสำหรับการทดสอบเพิ่มเติม (เป็นตัวเลือกที่ดีสำหรับ Laravel)
CREATE DATABASE IF NOT EXISTS `litelearning_testing`;
GRANT ALL PRIVILEGES ON `litelearning_testing`.* TO 'litelearning_user'@'%';

-- หมายเหตุ: ฐานข้อมูลหลัก "litelearning" จะถูกสร้างให้อัตโนมัติจากตัวแปร MYSQL_DATABASE ในไฟล์ docker-compose.yml 
-- ดังนั้นไม่ต้องใช้คำสั่ง CREATE DATABASE ซ้ำที่นี่

FLUSH PRIVILEGES;
