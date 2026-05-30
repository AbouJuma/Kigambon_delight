-- Fix POS 403 Unauthorized Issues
-- Run this script on your production database

-- Step 1: Add the missing Sales_pos permission
INSERT IGNORE INTO permissions (id, name) VALUES (139, 'Sales_pos');

-- Step 2: Assign Sales_pos permission to all existing roles
INSERT IGNORE INTO permission_role (permission_id, role_id)
SELECT 139, id FROM roles;

-- Step 3: Verify the changes
SELECT p.name as permission, r.name as role 
FROM permission_role pr
JOIN permissions p ON pr.permission_id = p.id
JOIN roles r ON pr.role_id = r.id
WHERE p.name = 'Sales_pos';
