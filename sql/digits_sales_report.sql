SELECT
    digits_sales.id,
    digits_sales.batch_number,
    digits_sales.is_final,
    digits_sales.reference_number AS reference_number, 
    all_items.digits_code IS NOT NULL as rr_flag,
    systems.system_name AS system_name,
    organizations.organization_name AS organization_name,
    report_types.report_type AS report_type,
    channels.channel_name AS channel_name,
    COALESCE(
        customers.customer_name,
        employees.employee_name
    ) AS customer_location,
    concepts.concept_name AS store_concept_name,
    digits_sales.receipt_number AS receipt_number,
    digits_sales.sales_date AS sales_date,
    apple_cutoffs.apple_yr_qtr_wk AS apple_yr_qtr_wk,
    apple_cutoffs.apple_week_cutoff AS apple_week_cutoff,
    non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk,
    non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff,
    DATE_FORMAT(digits_sales.sales_date, "%Y_%m") AS sales_date_yr_mo,
    DATE_FORMAT(digits_sales.sales_date, "%Y") AS sales_year,
    DATE_FORMAT(digits_sales.sales_date, "%m") AS sales_month,
    digits_sales.item_code AS item_code,
    digits_sales.item_description AS item_description,
    COALESCE(
        digits_sales.digits_code_rr_ref,
        digits_sales.item_code
    ) AS digits_code_rr_ref,
    all_items.digits_code AS digits_code,
    COALESCE(
        all_items.item_description,
        digits_sales.item_description
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
    digits_sales.quantity_sold AS quantity_sold,
    digits_sales.sold_price AS sold_price,
    digits_sales.qtysold_price AS qtysold_price,
    digits_sales.store_cost AS store_cost,
    digits_sales.qtysold_sc AS qtysold_sc,
    digits_sales.net_sales AS net_sales,
    digits_sales.sale_memo_reference AS sale_memo_reference,
    digits_sales.current_srp AS current_srp,
    digits_sales.qtysold_csrp AS qtysold_csrp,
    digits_sales.dtp_rf AS dtp_rf,
    digits_sales.qtysold_rf AS qtysold_rf,
    digits_sales.landed_cost AS landed_cost,
    digits_sales.qtysold_lc AS qtysold_lc,
    digits_sales.dtp_ecom AS dtp_ecom,
    digits_sales.qtysold_ecom AS qtysold_ecom
FROM digits_sales
    LEFT JOIN systems ON digits_sales.systems_id = systems.id
    LEFT JOIN organizations ON digits_sales.organizations_id = organizations.id
    LEFT JOIN report_types ON digits_sales.report_types_id = report_types.id
    LEFT JOIN channels ON digits_sales.channels_id = channels.id
    LEFT JOIN customers ON digits_sales.customers_id = customers.id
    LEFT JOIN concepts ON customers.concepts_id = concepts.id
    LEFT JOIN employees ON digits_sales.employees_id = employees.id
    LEFT JOIN apple_cutoffs ON digits_sales.sales_date = apple_cutoffs.sold_date
    LEFT JOIN non_apple_cutoffs ON digits_sales.sales_date = non_apple_cutoffs.sold_date
    LEFT JOIN all_items ON digits_sales.item_code = all_items.item_code