START TRANSACTION;

/* 카테고리 보강 (id 12~) */
INSERT INTO product_categories (id, label, created_at, updated_at) VALUES
 (12,'Frame',NOW(),NOW()),
 (13,'Headlight & Loupe Accessory',NOW(),NOW()),
 (14,'Headlight Accessory',NOW(),NOW()),
 (15,'Dermatoscope Accessory',NOW(),NOW());

/* 제품 템플릿 INSERT (USD, 설명 한글) */
INSERT INTO product_templates
(category_id, name, code, model, price, sort_order, description, created_at, updated_at)
VALUES
-- Ergo X (Loupe)
(2,'Ergo X','IAL-1040','IAL-1040',2745.00,1,'인체공학 각도형 TTL 루페(Ergo X), 장시간 착용에 최적화',NOW(),NOW()),

-- Galilean Loupes (동일명, 모델만 다름)
(2,'Galilean Loupes','ITL-1025G','ITL-1025G',1350.00,2,'TTL 갈릴레안 루페 2.5x 계열',NOW(),NOW()),
(2,'Galilean Loupes','ITL-1030G','ITL-1030G',1350.00,3,'TTL 갈릴레안 루페 3.0x 계열',NOW(),NOW()),
(2,'Galilean Loupes','ITL-1035G','ITL-1035G',1350.00,4,'TTL 갈릴레안 루페 3.5x 계열',NOW(),NOW()),

-- Prismatic (TTL)
(2,'Prismatic','ITL-1040P','ITL-1040P',1350.00,5,'TTL 프리즘 루페 4.0x 계열',NOW(),NOW()),
(2,'Prismatic','ITL-1045P','ITL-1045P',1350.00,6,'TTL 프리즘 루페 4.5x 계열',NOW(),NOW()),
(2,'Prismatic','ITL-1055P','ITL-1055P',1350.00,7,'TTL 프리즘 루페 5.5x 계열',NOW(),NOW()),
(2,'Prismatic','ITL-1065P','ITL-1065P',1350.00,8,'TTL 프리즘 루페 6.5x 계열',NOW(),NOW()),

-- Flip-Up Loupes
(2,'Flip-Up Loupes','IFL-1030G','IFL-1030G',605.00,9,'플립업 갈릴레안 루페',NOW(),NOW()),

-- Frame
(12,'Goggle - Rimless','GOGGLE_RIMLESS','Goggle - Rimless',0.00,10,'루페/헤드라이트용 고글 프레임(림리스)',NOW(),NOW()),
(12,'Goggle - Rimmed','GOGGLE_RIMMED','Goggle - Rimmed',0.00,11,'루페/헤드라이트용 고글 프레임(림 있음)',NOW(),NOW()),
(12,'Frame 1','FRAME_1','Frame 1',0.00,12,'스포츠형 프레임(색상 5종 옵션)',NOW(),NOW()),
(12,'Frame 2','FRAME_2','Frame 2',0.00,13,'메탈/스포츠 하이브리드 프레임(색상 3종)',NOW(),NOW()),
(12,'Frame 4','FRAME_4','Frame 4',0.00,14,'경량 프레임(색상 3종)',NOW(),NOW()),

-- Headlight & Loupe (본체)
(13,'IHL-1000 - wired','IHL-1000','IHL-1000 - wired',1050.00,15,'유선 헤드라이트, UV 필터 포함',NOW(),NOW()),
(13,'IHL-2000 - wireless','IHL-2000','IHL-2000 - wireless',1150.00,16,'무선 헤드라이트, 교체식 배터리',NOW(),NOW()),

-- Headlight Accessory
(14,'Additional Battery for Wired Headlight','BATTERY_WIRED_HEADLIGHT','Battery for IHL-1000',365.00,17,'유선 헤드라이트 전용 예비 배터리',NOW(),NOW()),
(14,'Additional Battery for Wireless Headlight','BATTERY_WIRELESS_HEADLIGHT','Battery for IHL-2000',150.00,18,'무선 헤드라이트 추가 배터리',NOW(),NOW()),
(14,'Headband','HEADBAND','Headband',210.00,19,'헤드라이트 전용 헤드밴드',NOW(),NOW()),
(14,'Frame for Headlights','FRAME_FOR_HEADLIGHTS','Frame for Headlight',180.00,20,'헤드라이트 장착용 프레임(헤드라이트 미포함)',NOW(),NOW()),
(14,'Universal Adapter','UNIVERSAL_ADAPTER','Universal Adapter',45.00,21,'헤드라이트 범용 어댑터',NOW(),NOW()),
(14,'Clamp Adapter','CLAMP_ADAPTER','Clamp Type Adapter',50.00,22,'헤드라이트 클램프형 어댑터',NOW(),NOW()),
(14,'Belt Clip for Wired Headlight','BELT_CLIP_WIRED','Belt Clip',25.00,23,'유선 헤드라이트 배터리 클립',NOW(),NOW()),
(14,'Extension Cable','EXTENSION_CABLE','Extension Cable',0.00,24,'연장 케이블(가격 미공개)',NOW(),NOW()),
(14,'Headstrap for Metal Frame','HEADSTRAP_METAL','Headstrap for Metal Frame',0.00,25,'메탈 프레임용 헤드스트랩',NOW(),NOW()),
(14,'Headstrap for Goggle','HEADSTRAP_GOGGLE','Headstrap for Goggle',0.00,26,'고글용 헤드스트랩',NOW(),NOW()),
(14,'Side Shield for Metal Frame','SIDE_SHIELD_METAL','Side Shield for Metal Frame',13.99,27,'메탈 프레임용 사이드 실드(유사품 참고가)',NOW(),NOW()),
(14,'Side Shield for Goggle','SIDE_SHIELD_GOGGLE','Side Shield for Goggle',13.99,28,'고글용 사이드 실드(유사품 참고가)',NOW(),NOW()),
(14,'Cleaning Kit','CLEANING_KIT_HEADLIGHT','Cleaning Kit',0.00,29,'클리닝 키트(가격 미공개)',NOW(),NOW()),
(14,'Screwdriver','SCREWDRIVER','Screwdriver',0.00,30,'소형 드라이버(가격 미공개)',NOW(),NOW()),
(14,'Nose Pad','NOSE_PAD','Nose Pad',0.00,31,'코패드(가격 미공개)',NOW(),NOW()),
(14,'U Shaped Nose Pad','U_SHAPED_NOSE_PAD','U-Shaped Nose Pad',0.00,32,'U자형 코패드(가격 미공개)',NOW(),NOW()),
(14,'UV Filter','UV_FILTER','UV Filter',0.00,33,'UV 필터(별도 판매가 미확인, 본체 동봉)',NOW(),NOW()),

-- Dental Mirror
(11,'DENTAL MIRROR','IDM-P','IDM-P',155.00,34,'치과 포토 미러(포토 미러 계열)',NOW(),NOW()),
(11,'DENTAL MIRROR','IDM-M','IDM-M',85.00,35,'치과 구강 미러(핸들 포함, 5팩 기준)',NOW(),NOW()),

-- IDS (Dermatoscope 본체)
(3,'IDS','IDS-1100','IDS-1100',950.00,36,'더마토스코프 1100, 10배/25mm 렌즈',NOW(),NOW()),
(3,'IDS','IDS-1000','IDS-1000',0.00,37,'더마토스코프 1000(단종/가격 미공개)',NOW(),NOW()),
(3,'IDS','IDS-3100','IDS-3100',850.00,38,'우드램프(365/395/405nm)',NOW(),NOW()),

-- Dermatoscope Accessory
(15,'Compact Universal Clamp','COMPACT_UNIVERSAL_CLAMP','Universal Phone Clamp',65.00,39,'스마트폰 고정 클램프(IDS-1100/1000+ 호환)',NOW(),NOW()),
(15,'8mm Small Contact Plate','CONTACT_PLATE_8MM','8mm Small Contact Plate',0.00,40,'8mm 소형 콘택트 플레이트(가격 미확인)',NOW(),NOW()),
(15,'Belt Clip Leather Pouch','LEATHER_POUCH','Leather Pouch w/ Belt Clip',30.00,41,'레더 파우치(벨트클립 포함)',NOW(),NOW()),
(15,'Sleeve with Lanyard (1100)','SLEEVE_1100','Sleeve with Lanyard (1100)',26.00,42,'실리콘 슬리브+랜야드(IDS-1100)',NOW(),NOW()),
(15,'Sleeve with Lanyard (1000)','SLEEVE_1000','Sleeve with Lanyard (1000)',26.00,43,'실리콘 슬리브+랜야드(IDS-1000)',NOW(),NOW()),
(15,'Replacement Battery (1100)','REPL_BATT_1100','Replacement Battery (1100)',75.00,44,'IDS-1100/1100C 교체 배터리(해외가 참고)',NOW(),NOW()),
(15,'Replacement Battery (1000)','REPL_BATT_1000','Replacement Battery (1000)',40.00,45,'IDS-1000 교체 배터리(해외가 참고)',NOW(),NOW()),
(15,'Replacement Battery (3100)','REPL_BATT_3100','Replacement Battery (3100)',160.00,46,'IDS-3100 교체 배터리(해외가 참고)',NOW(),NOW()),
(15,'Mirrorless Camera Adapter','MIRRORLESS_ADAPTER','Mirrorless Camera Adapter',260.00,47,'미러리스 카메라 어댑터',NOW(),NOW()),
(15,'Protective Glass (1100)','PROTECTIVE_GLASS_1100','Protective Glass (1100)',150.00,48,'IDS-1100/1100C 보호유리(호환 렌즈)',NOW(),NOW()),
(15,'Protective Glass (1000)','PROTECTIVE_GLASS_1000','Protective Glass (1000)',0.00,49,'IDS-1000 보호유리(가격 미확인)',NOW(),NOW()),
(15,'USB Cable','USB_CABLE','USB Cable',0.00,50,'USB 충전 케이블(가격 미공개)',NOW(),NOW()),
(15,'Magnet USB charging Cable for 1100C','MAGNET_USB_CABLE_1100C','Magnet USB Cable (1100C)',0.00,51,'마그넷 USB 충전 케이블(1100C)',NOW(),NOW()),
(15,'Cleaning Kit','CLEANING_KIT_DERMATO','Cleaning Kit',0.00,52,'클리닝 키트(가격 미공개)',NOW(),NOW());

COMMIT;
