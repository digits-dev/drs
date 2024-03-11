SELECT
    warehouse_inventories.id,
    warehouse_inventories.batch_number,
    warehouse_inventories.is_final,
    warehouse_inventories.reference_number,
    systems.system_name AS system_name,
    organizations.organization_name AS organization_name,
    report_types.report_type AS report_type,
    inventory_transaction_types.inventory_transaction_type AS inventory_transaction_type,
    channels.channel_name AS channel_name,
    COALESCE(
        customers.customer_name,
        employees.employee_name
    ) AS customer_location,
    concepts.concept_name AS store_concept_name,
    warehouse_inventories.inventory_date AS inventory_date,
    warehouse_inventories.item_code AS item_code,
    warehouse_inventories.item_description AS item_description,
    all_items.digits_code AS digits_code,
    COALESCE(
        all_items.item_description,
        warehouse_inventories.item_description
    ) AS imfs_item_description,
    all_items.upc_code AS upc_code,
    all_items.upc_code2 AS upc_code2,
    all_items.upc_code3 AS upc_code3,
    all_items.upc_code4 AS upc_code4,
    all_items.upc_code5 AS upc_code5,
    all_items.brand_description AS brand_description,
    all_items.category_description AS category_description,
    all_items.margin_category_description AS margin_category_description,
    all_items.vendor_type_code AS vendor_type_code,
    all_items.inventory_type_description AS inventory_type_description,
    all_items.sku_status_description AS sku_status_description,
    all_items.brand_status AS brand_status,
    warehouse_inventories.quantity_inv AS quantity_inv,
    all_items.current_srp AS current_srp, (
        all_items.current_srp * warehouse_inventories.quantity_inv
    ) AS qtyinv_srp,
    warehouse_inventories.store_cost AS store_cost,
    warehouse_inventories.qtyinv_sc AS qtyinv_sc,
    warehouse_inventories.dtp_rf AS dtp_rf,
    warehouse_inventories.qtyinv_rf AS qtyinv_rf,
    warehouse_inventories.landed_cost AS landed_cost,
    warehouse_inventories.qtyinv_lc AS qtyinv_lc,
    warehouse_inventories.qtyinv_ecom as qtyinv_ecom
FROM warehouse_inventories
    LEFT JOIN systems ON warehouse_inventories.systems_id = systems.id
    LEFT JOIN organizations ON warehouse_inventories.organizations_id = organizations.id
    LEFT JOIN report_types ON warehouse_inventories.report_types_id = report_types.id
    LEFT JOIN inventory_transaction_types ON warehouse_inventories.inventory_transaction_types_id = inventory_transaction_types.id
    LEFT JOIN channels ON warehouse_inventories.channels_id = channels.id
    LEFT JOIN customers ON warehouse_inventories.customers_id = customers.id
    LEFT JOIN concepts ON customers.concepts_id = concepts.id
    LEFT JOIN employees ON warehouse_inventories.employees_id = employees.id
    LEFT JOIN all_items ON warehouse_inventories.item_code = all_items.item_code