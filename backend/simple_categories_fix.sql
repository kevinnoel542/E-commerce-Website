-- Simple fix for categories table error
-- Run this in your Supabase SQL Editor

-- 1. Create categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    description TEXT,
    parent_id UUID REFERENCES categories(id),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    created_by UUID REFERENCES auth.users(id),
    updated_by UUID REFERENCES auth.users(id)
);

-- 2. Enable Row Level Security for categories
ALTER TABLE categories ENABLE ROW LEVEL SECURITY;

-- 3. Drop existing policies if they exist (to avoid conflicts)
DROP POLICY IF EXISTS "Anyone can view active categories" ON categories;
DROP POLICY IF EXISTS "Authenticated users can manage categories" ON categories;

-- 4. Create policies for categories
CREATE POLICY "Anyone can view active categories" ON categories
    FOR SELECT USING (is_active = true);

CREATE POLICY "Authenticated users can manage categories" ON categories
    FOR ALL USING (auth.role() = 'authenticated');

-- 5. Create some sample categories (simple approach)
INSERT INTO categories (name, description, is_active) 
SELECT 'Electronics', 'Electronic devices and gadgets', true
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Electronics');

INSERT INTO categories (name, description, is_active) 
SELECT 'Clothing', 'Fashion and apparel', true
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Clothing');

INSERT INTO categories (name, description, is_active) 
SELECT 'Books', 'Books and literature', true
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Books');

INSERT INTO categories (name, description, is_active) 
SELECT 'Home & Garden', 'Home improvement and gardening supplies', true
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Home & Garden');

INSERT INTO categories (name, description, is_active) 
SELECT 'Sports', 'Sports equipment and accessories', true
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Sports');

-- 6. Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_categories_active ON categories(is_active);
CREATE INDEX IF NOT EXISTS idx_categories_parent ON categories(parent_id);
CREATE INDEX IF NOT EXISTS idx_categories_name ON categories(name);

-- 7. Grant permissions
GRANT USAGE ON SCHEMA public TO anon, authenticated;
GRANT SELECT ON categories TO anon, authenticated;
GRANT ALL ON categories TO authenticated;

-- 8. Show results
SELECT 'Categories setup completed!' as status;
SELECT COUNT(*) as total_categories FROM categories;
SELECT name, description FROM categories WHERE is_active = true ORDER BY name;
