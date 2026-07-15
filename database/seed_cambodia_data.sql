-- ============================================================
-- CAMBODIA WAREHOUSE INVENTORY MANAGEMENT
-- Seed Data for Suppliers, Customers & Inventory Items
-- ============================================================

-- ============================================================
-- 1. SUPPLIERS DATA
-- Local Cambodian suppliers
-- ============================================================
INSERT INTO `suppliers` (`supplier_code`, `supplier_name`, `phone`, `email`, `state`, `city`, `address`, `balance`, `created_at`, `updated_at`) VALUES
('SUP001', 'Sokha Import Export Co., Ltd.', '012 345 678', 'info@sokhaimport.com.kh', 'Phnom Penh', 'Phnom Penh', '#123, Street 271, Sangkat Toul Svay Prey, Khan Chamkarmon', 0.00, NOW(), NOW()),
('SUP002', 'Angkor Distribution Center', '015 678 901', 'order@angkordist.com.kh', 'Siem Reap', 'Siem Reap', 'Road 6, Krous Village, Sangkat Slorkram', 0.00, NOW(), NOW()),
('SUP003', 'Kirirom Food Supply Co.', '017 234 567', 'sales@kiriromfood.com.kh', 'Kampong Speu', 'Kirirom', '#45, National Road 4, Kirirom District', 0.00, NOW(), NOW()),
('SUP004', 'Mekong Industrial Parts', '096 789 0123', 'info@mekongparts.com.kh', 'Phnom Penh', 'Phnom Penh', '#88, Russian Federation Blvd, Sangkat Toek Laork, Khan Tuol Kork', 0.00, NOW(), NOW()),
('SUP005', 'Tonle Sap Trading Co.', '012 890 123', 'tonlesap.trade@gmail.com', 'Battambang', 'Battambang', '#20, Street 1, Sangkat Svay Por', 0.00, NOW(), NOW()),
('SUP006', 'Phnom Penh Construction Materials', '069 456 789', 'ppconstruct@online.com.kh', 'Phnom Penh', 'Phnom Penh', '#200, Mao Tse Tung Blvd, Sangkat Tuol Svay Prey', 0.00, NOW(), NOW()),
('SUP007', 'Kampuchea Beverages Import', '012 567 890', 'info@kbbeverage.com.kh', 'Phnom Penh', 'Phnom Penh', '#55, Street 598, Sangkat Boeung Kak II, Khan Tuol Kork', 0.00, NOW(), NOW()),
('SUP008', 'Coconut Kraft Cambodia', '097 123 4567', 'coconutkraft@cambodia.com', 'Kampot', 'Kampot', '#12, Riverside Road, Kampot City', 0.00, NOW(), NOW()),
('SUP009', 'Preah Vihear Agri Products', '088 234 5678', 'pv.agri@yahoo.com', 'Preah Vihear', 'Tbaeng Meanchey', '#78, National Road 62, Preah Vihear Province', 0.00, NOW(), NOW()),
('SUP010', 'Sihanoukville Logistics Hub', '016 789 012', 'sihamlogistics@gmail.com', 'Preah Sihanouk', 'Sihanoukville', '#5, Independence Street, Sangkat 4', 0.00, NOW(), NOW()),
('SUP011', 'Banteay Meanchey Rice Mill', '095 678 9012', 'bmrice@khmermill.com', 'Banteay Meanchey', 'Poipet', '#34, National Road 5, Poipet City', 0.00, NOW(), NOW()),
('SUP012', 'Koh Kong Seafood Supply', '012 345 123', 'seafood.kohkong@gmail.com', 'Koh Kong', 'Koh Kong City', '#88, Street 4, Smach Mean Chey', 0.00, NOW(), NOW()),
('SUP013', 'Pursat Natural Rubber Co.', '097 345 6789', 'pursatrubber@cambodia.com', 'Pursat', 'Pursat', '#56, National Road 5, Pursat City', 0.00, NOW(), NOW()),
('SUP014', 'Kampong Cham Trading Post', '096 456 7890', 'kc.trading@online.com.kh', 'Kampong Cham', 'Kampong Cham', '#22, Street 3, Sangkat Kampong Cham', 0.00, NOW(), NOW()),
('SUP015', 'Royal Electronics (Cambodia)', '012 789 456', 'info@royalelectronic.com.kh', 'Phnom Penh', 'Phnom Penh', '#333, Monivong Blvd, Sangkat Phsar Thmei III, Khan Daun Penh', 0.00, NOW(), NOW()),
('SUP016', 'Takeo Handicraft & Supply', '088 567 8901', 'takeo.handicraft@gmail.com', 'Takeo', 'Takeo', '#15, Street 10, Takeo City', 0.00, NOW(), NOW()),
('SUP017', 'Kampong Thom Hardware', '097 012 3456', 'kthardware@yahoo.com', 'Kampong Thom', 'Kampong Thom City', '#9A, National Road 6, Kampong Thom', 0.00, NOW(), NOW()),
('SUP018', 'Ratanakiri Fresh Produce', '017 888 999', 'rkt.fresh@gmail.com', 'Ratanakiri', 'Banlung', '#23, Street 78, Banlung City', 0.00, NOW(), NOW()),
('SUP019', 'Kandal Packing Solutions', '012 333 444', 'kandalpack@online.com.kh', 'Kandal', 'Ta Khmao', '#66, National Road 2, Ta Khmao City', 0.00, NOW(), NOW()),
('SUP020', 'Mondulkiri Organic Farm', '096 777 8888', 'mondul.organic@gmail.com', 'Mondulkiri', 'Senmonorom', '#101, Street 5, Senmonorom City', 0.00, NOW(), NOW());

-- ============================================================
-- 2. CUSTOMERS DATA
-- Local Cambodian customers
-- ============================================================
INSERT INTO `customers` (`customer_code`, `customer_name`, `phone`, `email`, `state`, `city`, `address`, `balance`, `created_at`, `updated_at`) VALUES
('CUS001', 'NagaWorld Hotel & Entertainment', '023 228 822', 'naga.purchase@nagaworld.com', 'Phnom Penh', 'Phnom Penh', '#1, Samdach Hun Sen Park, Sangkat Tonle Bassac, Khan Chamkarmon', 0.00, NOW(), NOW()),
('CUS002', 'Sokha Hotel Phnom Penh', '023 216 666', 'procurement@sokhahotels.com.kh', 'Phnom Penh', 'Phnom Penh', '#1, Street 238, Sangkat Chey Chumneah, Khan Daun Penh', 0.00, NOW(), NOW()),
('CUS003', 'Aeon Mall (Cambodia) Co., Ltd.', '023 963 000', 'aeon.receiving@aeonmall.com.kh', 'Phnom Penh', 'Phnom Penh', '#132, Samdech Techo Hun Sen Blvd, Sangkat Phnom Penh Thmey', 0.00, NOW(), NOW()),
('CUS004', 'Smart Axiata Co., Ltd.', '023 990 012', 'smart.ops@smart.com.kh', 'Phnom Penh', 'Phnom Penh', '#66, Preah Monivong Blvd, Sangkat Srah Chak, Khan Daun Penh', 0.00, NOW(), NOW()),
('CUS005', 'Cambodia Brewery Ltd.', '023 430 380', 'cb.supply@cambrew.com.kh', 'Phnom Penh', 'Phnom Penh', 'No. 167, Street 211, Sangkat Beoung Raing, Khan Dangkor', 0.00, NOW(), NOW()),
('CUS006', 'Angkor Wat Restaurant Group', '063 963 111', 'awr.purchase@gmail.com', 'Siem Reap', 'Siem Reap', '#88, Pub Street, Old Market Area, Siem Reap', 0.00, NOW(), NOW()),
('CUS007', 'Chip Mong Retail Co., Ltd.', '023 860 111', 'cm.inventory@chipmong.com', 'Phnom Penh', 'Phnom Penh', '#71, Preah Monivong Blvd, Sangkat Srah Chak, Khan Daun Penh', 0.00, NOW(), NOW()),
('CUS008', 'Kirirom Institute of Technology', '031 888 000', 'admin@kit.edu.kh', 'Kampong Speu', 'Kirirom', 'Kirirom National Park, Kampong Speu Province', 0.00, NOW(), NOW()),
('CUS009', 'Royal Railway (Cambodia)', '023 224 144', 'rr.procurement@royalrailway.com', 'Phnom Penh', 'Phnom Penh', '#26, Street 106, Sangkat Srah Chak, Khan Daun Penh', 0.00, NOW(), NOW()),
('CUS010', 'Lucky Supermarket (Cambodia)', '023 996 666', 'lucky.warehouse@luckysuper.com.kh', 'Phnom Penh', 'Phnom Penh', '#145, Preah Sihanouk Blvd, Sangkat Tonle Bassac', 0.00, NOW(), NOW()),
('CUS011', 'Sihanoukville Casino Group', '034 999 888', 'scg.fnb@svcgroup.com', 'Preah Sihanouk', 'Sihanoukville', '#55, Ekreach Street, Sangkat 3, Sihanoukville', 0.00, NOW(), NOW()),
('CUS012', 'Phnom Penh International Hospital', '023 216 911', 'purchasing@ppih.com.kh', 'Phnom Penh', 'Phnom Penh', '#888, Street 114, Sangkat Srah Chak, Khan Daun Penh', 0.00, NOW(), NOW()),
('CUS013', 'Cambodia University of Technology', '023 880 390', 'camt.tech@camtech.edu.kh', 'Phnom Penh', 'Phnom Penh', 'Russian Federation Blvd, Sangkat Toek Laork, Khan Tuol Kork', 0.00, NOW(), NOW()),
('CUS014', 'Pizza Company (Cambodia) Ltd.', '023 900 900', 'pcc.ops@thepizzacompany.com.kh', 'Phnom Penh', 'Phnom Penh', '#50, Street 294, Sangkat Tonle Bassac, Khan Chamkarmon', 0.00, NOW(), NOW()),
('CUS015', 'Battambang Provincial Hospital', '053 952 999', 'bpurchasing@battambang.gov.kh', 'Battambang', 'Battambang', '#1, Road 155, Battambang City', 0.00, NOW(), NOW()),
('CUS016', 'Kampot Pepper Association', '012 777 333', 'kpa.order@kampotpepper.com', 'Kampot', 'Kampot', '#33, Old Market Street, Kampot City', 0.00, NOW(), NOW()),
('CUS017', 'Chen Zhi Group (Prince Holding)', '023 888 777', 'procurement@princegroup.com', 'Phnom Penh', 'Phnom Penh', '#77, Canadian Plaza, Street 294, Sangkat Tonle Bassac', 0.00, NOW(), NOW()),
('CUS018', 'Sunrise Khmer Restaurant', '016 555 444', 'sunrise.khmer.fb@gmail.com', 'Siem Reap', 'Siem Reap', '#102, Stung Thmey Street, Siem Reap', 0.00, NOW(), NOW()),
('CUS019', 'Orussey Market Vendor Group', '012 444 555', 'orussey.group@gmail.com', 'Phnom Penh', 'Phnom Penh', 'Orussey Market, Street 217, Sangkat Orussey I, Khan 7 Makara', 0.00, NOW(), NOW()),
('CUS020', 'Khmer Fresh Mart (Kampot)', '017 222 333', 'fresmart.kampot@gmail.com', 'Kampot', 'Kampot', '#16, Riverside Road, Kampot City', 0.00, NOW(), NOW());

-- ============================================================
-- 3. INVENTORY ITEMS DATA
-- Cambodia warehouse inventory - mixed categories
-- ============================================================
INSERT INTO `inventory_items` (`item_code`, `item_name`, `category`, `quantity`, `unit_cost`, `selling_price`, `reorder_level`, `created_at`, `updated_at`) VALUES
-- Beverages & Drinks
('ITM001', 'Angkor Beer (Case of 24 cans)', 'Beverages', 500, 12.00, 18.50, 100, NOW(), NOW()),
('ITM002', 'Cambodia Beer (Case of 24 bottles)', 'Beverages', 320, 14.50, 22.00, 80, NOW(), NOW()),
('ITM003', 'Anchor Beer (Case of 24 cans)', 'Beverages', 450, 11.00, 16.50, 100, NOW(), NOW()),
('ITM004', 'Coca-Cola 1.5L (Case of 12)', 'Beverages', 600, 8.00, 12.00, 120, NOW(), NOW()),
('ITM005', 'Sting Energy Drink (Case of 48)', 'Beverages', 800, 10.00, 14.50, 150, NOW(), NOW()),
('ITM006', 'Angkor Pure Drinking Water 1.5L (Pack of 12)', 'Beverages', 1000, 3.50, 5.00, 200, NOW(), NOW()),

-- Rice & Grains
('ITM007', 'Premium Jasmine Rice (50kg bag)', 'Grains', 200, 28.00, 38.00, 30, NOW(), NOW()),
('ITM008', 'Cambodia Fragrant Rice (25kg bag)', 'Grains', 350, 15.00, 22.00, 50, NOW(), NOW()),
('ITM009', 'Organic Brown Rice (5kg pack)', 'Grains', 150, 5.00, 8.00, 30, NOW(), NOW()),
('ITM010', 'Glutinous Sticky Rice (1kg pack)', 'Grains', 400, 2.00, 3.50, 80, NOW(), NOW()),

-- Cooking Essentials
('ITM011', 'Palm Oil (5L bottle)', 'Cooking Essentials', 250, 6.50, 9.50, 40, NOW(), NOW()),
('ITM012', 'Fish Sauce - Kapik (1L bottle)', 'Cooking Essentials', 300, 2.50, 4.00, 60, NOW(), NOW()),
('ITM013', 'Soya Sauce - Knor (1L bottle)', 'Cooking Essentials', 200, 2.00, 3.50, 50, NOW(), NOW()),
('ITM014', 'Sugar - Kampong Speu (1kg pack)', 'Cooking Essentials', 500, 1.20, 2.00, 100, NOW(), NOW()),
('ITM015', 'Prahok (Fermented Fish Paste - 500g)', 'Cooking Essentials', 180, 1.80, 3.00, 40, NOW(), NOW()),
('ITM016', 'Salt (1kg pack)', 'Cooking Essentials', 600, 0.50, 1.00, 120, NOW(), NOW()),

-- Kampot Pepper & Spices
('ITM017', 'Kampot Black Pepper (100g pack)', 'Spices', 400, 3.50, 6.50, 60, NOW(), NOW()),
('ITM018', 'Kampot Red Pepper (100g pack)', 'Spices', 250, 4.00, 7.00, 40, NOW(), NOW()),
('ITM019', 'Kampot White Pepper (100g pack)', 'Spices', 200, 4.50, 7.50, 30, NOW(), NOW()),
('ITM020', 'Lemongrass (fresh - 1kg)', 'Spices', 120, 1.50, 2.80, 25, NOW(), NOW()),

-- Cleaning & Household
('ITM021', 'Laundry Detergent (2kg pack)', 'Household', 350, 4.00, 6.50, 70, NOW(), NOW()),
('ITM022', 'Dishwashing Liquid (1L bottle)', 'Household', 400, 2.00, 3.50, 80, NOW(), NOW()),
('ITM023', 'Household Bleach (1L bottle)', 'Household', 300, 1.50, 2.50, 60, NOW(), NOW()),
('ITM024', 'Mosquito Coil (10-pack)', 'Household', 600, 1.00, 1.80, 100, NOW(), NOW()),

-- Fruits & Vegetables (Fresh supply)
('ITM025', 'Cambodian Mangoes - Keo Romeat (1kg)', 'Fresh Produce', 100, 2.00, 3.50, 20, NOW(), NOW()),
('ITM026', 'Dragon Fruit - White Flesh (1kg)', 'Fresh Produce', 80, 3.00, 5.00, 15, NOW(), NOW()),
('ITM027', 'Bananas - Chek Pong (bunch)', 'Fresh Produce', 200, 1.00, 2.00, 40, NOW(), NOW()),
('ITM028', 'Pineapples - Kampot (each)', 'Fresh Produce', 120, 1.50, 2.50, 25, NOW(), NOW()),
('ITM029', 'Watermelon (large, each)', 'Fresh Produce', 90, 2.50, 4.00, 15, NOW(), NOW()),

-- Seafood & Meat (Frozen)
('ITM030', 'Freshwater Fish - Mekong (1kg)', 'Meat & Seafood', 150, 3.00, 5.00, 30, NOW(), NOW()),
('ITM031', 'Shrimp - Kampot (1kg frozen)', 'Meat & Seafood', 80, 8.00, 12.00, 15, NOW(), NOW()),
('ITM032', 'Chicken - Whole (1kg)', 'Meat & Seafood', 200, 3.50, 5.50, 40, NOW(), NOW()),
('ITM033', 'Pork Belly (1kg)', 'Meat & Seafood', 180, 4.00, 6.50, 35, NOW(), NOW()),
('ITM034', 'Beef - Imported (1kg)', 'Meat & Seafood', 60, 8.50, 12.00, 10, NOW(), NOW()),

-- Construction & Hardware
('ITM035', 'Cement - Chip Mong (50kg bag)', 'Construction', 500, 5.00, 7.50, 100, NOW(), NOW()),
('ITM036', 'Steel Rebar 12mm (6m length)', 'Construction', 300, 8.00, 11.00, 50, NOW(), NOW()),
('ITM037', 'Red Brick (per 100 pieces)', 'Construction', 200, 12.00, 16.00, 40, NOW(), NOW()),
('ITM038', 'Sand (1 cubic meter)', 'Construction', 100, 15.00, 22.00, 20, NOW(), NOW()),
('ITM039', 'Paint - Kansai (5L pail)', 'Construction', 120, 12.00, 17.00, 25, NOW(), NOW()),
('ITM040', 'PVC Pipe 4 inch (4m length)', 'Construction', 250, 3.50, 5.50, 50, NOW(), NOW()),

-- Office Supplies
('ITM041', 'Printer Paper A4 (5-ream box)', 'Office Supplies', 150, 18.00, 25.00, 30, NOW(), NOW()),
('ITM042', 'Ballpoint Pen - Box of 50', 'Office Supplies', 200, 6.00, 9.50, 40, NOW(), NOW()),
('ITM043', 'Liquid Glue (100ml bottle)', 'Office Supplies', 300, 0.80, 1.50, 60, NOW(), NOW()),

-- Personal Care
('ITM044', 'Toothbrush - Soft (pack of 3)', 'Personal Care', 350, 2.00, 3.50, 70, NOW(), NOW()),
('ITM045', 'Body Soap - Lux (135g bar)', 'Personal Care', 500, 0.80, 1.50, 100, NOW(), NOW()),
('ITM046', 'Shampoo - Sunsilk (350ml bottle)', 'Personal Care', 280, 2.50, 4.00, 50, NOW(), NOW()),

-- Textiles & Clothing
('ITM047', 'Krama - Traditional Khmer Scarf', 'Textiles', 400, 2.00, 4.00, 60, NOW(), NOW()),
('ITM048', 'T-Shirt - Plain Cotton (each)', 'Textiles', 300, 3.00, 5.50, 50, NOW(), NOW()),
('ITM049', 'Sarong - Traditional (each)', 'Textiles', 150, 5.00, 8.00, 25, NOW(), NOW()),

-- Electronics & Appliances
('ITM050', 'LED Light Bulb 12W (each)', 'Electronics', 500, 1.50, 3.00, 100, NOW(), NOW()),
('ITM051', 'Extension Cord 5m (each)', 'Electronics', 200, 3.00, 5.00, 40, NOW(), NOW()),
('ITM052', 'Electric Fan - Stand (18 inch)', 'Electronics', 80, 15.00, 22.00, 15, NOW(), NOW()),
('ITM053', 'IRadio - Portable (each)', 'Electronics', 60, 8.00, 12.00, 10, NOW(), NOW()),

-- Motorcycle / Vehicle Supplies
('ITM054', 'Motorcycle Helmet - Half Face', 'Vehicle Supplies', 100, 8.00, 15.00, 20, NOW(), NOW()),
('ITM055', 'Engine Oil Shell 4T (1L bottle)', 'Vehicle Supplies', 200, 5.00, 7.50, 40, NOW(), NOW()),
('ITM056', 'Car Tire - 205/55R16 (each)', 'Vehicle Supplies', 40, 55.00, 75.00, 8, NOW(), NOW()),

-- Pet Supplies
('ITM057', 'Dog Food (15kg bag)', 'Pet Supplies', 80, 18.00, 25.00, 15, NOW(), NOW()),
('ITM058', 'Cat Food (10kg bag)', 'Pet Supplies', 60, 15.00, 22.00, 10, NOW(), NOW());
