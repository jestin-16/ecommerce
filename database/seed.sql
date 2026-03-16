-- INSERT 12+ luxury winter fashion products
USE shop_db;

INSERT INTO products (name, description, price, image_url, category, stock) VALUES
-- Coats & Jackets
('Midnight Frost Turtleneck', 'Discover the epitome of winter luxury. Uncompromising warmth meets avant-garde design in our latest collection.', 895.00, 'images/image.png', 'Knitwear', 50),
('Arctic Wool Overcoat', 'A timeless winter essential. Constructed from pure arctic wool featuring a tailored fit and thermal lining.', 1250.00, 'images/image.png', 'Coats & Jackets', 15),
('Glacier Silk Scarf', 'Icy elegance wrapped in delicate silk. The perfect statement piece to elevate your midnight winter wardrobe.', 340.00, 'images/image2.png', 'Accessories', 0),
('Frostbite Leather Gloves', 'Premium hand-stitched leather gloves lined with cashmere to keep the freezing cold at bay without sacrificing style.', 425.00, 'images/image3.png', 'Accessories', 120),
('Obsidian Down Puffer', 'Ultimate warmth encased in an obsidian shell. Designed for extreme cold while maintaining a sleek silhouette.', 1450.00, 'images/image1.png', 'Coats & Jackets', 10),
('Alpine Cashmere Sweater', 'Incredibly soft and lightweight cashmere sweater perfect for layering during harsh mountain winters.', 750.00, 'images/image.png', 'Knitwear', 8),
('Boreal Hiking Boots', 'Engineered for the frozen terrain. Featuring a vibram sole and waterproof membrane for ultimate performance.', 580.00, 'https://images.unsplash.com/photo-1520639889410-1eb41d8efff3?auto=format&fit=crop&q=80&w=800', 'Footwear', 25);
