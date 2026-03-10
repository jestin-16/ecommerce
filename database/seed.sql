-- INSERT 12+ realistic products across 4 categories
USE shop_db;

INSERT INTO products (name, description, price, image_url, category, stock) VALUES
-- Electronics
('Wireless Noise-Canceling Headphones', 'Immerse yourself in music with these premium over-ear headphones featuring active noise cancellation and 30-hour battery life.', 199.99, 'https://placehold.co/300x200/292b2c/ffffff?text=Headphones', 'Electronics', 50),
('4K Ultra HD Smart TV', 'Experience stunning picture quality with this 55-inch 4K Smart TV. Built-in streaming apps and HDR support.', 499.00, 'https://placehold.co/300x200/17a2b8/ffffff?text=Smart+TV', 'Electronics', 15),
('Ultra-Thin Laptop', 'Powerful and portable. Featuring a modern processor, 16GB RAM, and 512GB SSD for all your professional needs.', 899.50, 'https://placehold.co/300x200/6f42c1/ffffff?text=Laptop', 'Electronics', 0),

-- Clothing
('Classic Denim Jacket', 'A timeless wardrobe essential. This blue denim jacket pair well with any casual outfit. 100% cotton.', 59.90, 'https://placehold.co/300x200/ffc107/343a40?text=Denim+Jacket', 'Clothing', 120),
('Performance Running Shoes', 'Lightweight and breathable sneakers designed for ultimate comfort and durability on long runs.', 119.99, 'https://placehold.co/300x200/dc3545/ffffff?text=Running+Shoes', 'Clothing', 10),
('Cotton V-Neck T-Shirt', 'Soft, breathable organic cotton t-shirt. Available in multiple colors for everyday wear.', 19.99, 'https://placehold.co/300x200/28a745/ffffff?text=Cotton+T-Shirt', 'Clothing', 200),

-- Books
('The Art of Programming', 'A comprehensive guide to writing clean, maintainable, and efficient code in modern software ecosystems.', 45.00, 'https://placehold.co/300x200/343a40/ffffff?text=Programming+Book', 'Books', 30),
('Healthy Eating Cookbook', 'Over 100 delicious and nutritious recipes for quick weeknight dinners and special occasions.', 24.95, 'https://placehold.co/300x200/e83e8c/ffffff?text=Cookbook', 'Books', 85),
('Mystery of the Old Manor', 'A thrilling fiction novel following a detective uncovering deep secrets in a highly suspenseful setting.', 15.50, 'https://placehold.co/300x200/6c757d/ffffff?text=Mystery+Novel', 'Books', 60),

-- Home
('Ceramic Coffee Mug Set', 'Set of 4 artisan-crafted ceramic mugs. Microwave and dishwasher safe with a beautiful reactive glaze.', 34.00, 'https://placehold.co/300x200/fd7e14/ffffff?text=Mug+Set', 'Home', 40),
('Memory Foam Pillow', 'Wake up refreshed with this ergonomic memory foam pillow that contours to your neck and head.', 49.99, 'https://placehold.co/300x200/007bff/ffffff?text=Memory+Pillow', 'Home', 25),
('Indoor Potted Plant', 'A low-maintenance indoor houseplant in a decorative modern ceramic pot. Perfect for brightening any room.', 29.50, 'https://placehold.co/300x200/20c997/ffffff?text=Potted+Plant', 'Home', 5);
