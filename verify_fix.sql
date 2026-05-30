-- Verify the Sales_pos permission fix on live database
SELECT p.name as permission, r.name as role 
FROM permission_role pr
JOIN permissions p ON pr.permission_id = p.id
JOIN roles r ON pr.role_id = r.id
WHERE p.name = 'Sales_pos';
