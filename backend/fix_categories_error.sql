-- Quick fix for categories table error
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

-- 5. Create some sample categories (only if they don't exist)
DO $$
BEGIN
    -- Check if categories already exist
    IF NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Electronics') THEN
        INSERT INTO categories (name, description, is_active) VALUES
            ('Electronics', 'Electronic devices and gadgets', true),
            ('Clothing', 'Fashion and apparel', true),
            ('Books', 'Books and literature', true),
            ('Home & Garden', 'Home improvement and gardening supplies', true),
            ('Sports', 'Sports equipment and accessories', true);
        RAISE NOTICE 'Sample categories created successfully!';
    ELSE
        RAISE NOTICE 'Categories already exist, skipping sample data creation.';
    END IF;
END $$;

-- 6. Create index for better performance
CREATE INDEX IF NOT EXISTS idx_categories_active ON categories(is_active);
CREATE INDEX IF NOT EXISTS idx_categories_parent ON categories(parent_id);

-- 7. Grant permissions
GRANT USAGE ON SCHEMA public TO anon, authenticated;
GRANT SELECT ON categories TO anon, authenticated;
GRANT ALL ON categories TO authenticated;

-- 8. Verify the setup
DO $$
DECLARE
    category_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO category_count FROM categories WHERE is_active = true;
    RAISE NOTICE 'Categories table setup completed successfully!';
    RAISE NOTICE 'Active categories: %', category_count;
END $$;
