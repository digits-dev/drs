SELECT
    store_sales.id,
    store_sales.batch_number,
    store_sales.is_final,
    store_sales.reference_number,
    all_items.digits_code IS NOT NULL AS rr_flag,
    systems.system_name AS system_name,
    organizations.organization_name AS organization_name,
    report_types.report_type AS report_type,
    channels.channel_name AS channel_name,
    COALESCE(
        customers.customer_name,
        employees.employee_name
    ) AS customer_location,
    concepts.concept_name AS store_concept_name,
    store_sales.receipt_number AS receipt_number,
    store_sales.sales_date AS sales_date,
    apple_cutoffs.apple_yr_qtr_wk AS apple_yr_qtr_wk,
    apple_cutoffs.apple_week_cutoff AS apple_week_cutoff,
    non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk,
    non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff,
    DATE_FORMAT(store_sales.sales_date, "%Y_%m") AS sales_date_yr_mo,
    DATE_FORMAT(store_sales.sales_date, "%Y") AS sales_year,
    DATE_FORMAT(store_sales.sales_date, "%m") AS sales_month,
    store_sales.item_code AS item_code,
    store_sales.item_description AS item_description,
    COALESCE(
        store_sales.digits_code_rr_ref,
        store_sales.item_code
    ) AS digits_code_rr_ref,
    all_items.digits_code AS digits_code,
    COALESCE(
        all_items.item_description,
        store_sales.item_description
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
    store_sales.quantity_sold AS quantity_sold,
    store_sales.sold_price AS sold_price,
    store_sales.qtysold_price AS qtysold_price,
    store_sales.store_cost AS store_cost,
    store_sales.qtysold_sc AS qtysold_sc,
    store_sales.net_sales AS net_sales,
    store_sales.sale_memo_reference AS sale_memo_reference,
    store_sales.current_srp AS current_srp,
    store_sales.qtysold_csrp AS qtysold_csrp,
    store_sales.dtp_rf AS dtp_rf,
    store_sales.qtysold_rf AS qtysold_rf,
    store_sales.landed_cost AS landed_cost,
    store_sales.qtysold_lc AS qtysold_lc,
    store_sales.dtp_ecom AS dtp_ecom,
    store_sales.qtysold_ecom AS qtysold_ecom
FROM store_sales
    LEFT JOIN systems ON store_sales.systems_id = systems.id
    LEFT JOIN organizations ON store_sales.organizations_id = organizations.id
    LEFT JOIN report_types ON store_sales.report_types_id = report_types.id
    LEFT JOIN channels ON store_sales.channels_id = channels.id
    LEFT JOIN customers ON store_sales.customers_id = customers.id
    LEFT JOIN concepts ON customers.concepts_id = concepts.id
    LEFT JOIN employees ON store_sales.employees_id = employees.id
    LEFT JOIN apple_cutoffs ON store_sales.sales_date = apple_cutoffs.sold_date
    LEFT JOIN non_apple_cutoffs ON store_sales.sales_date = non_apple_cutoffs.sold_date
    LEFT JOIN all_items ON store_sales.item_code = all_items.item_code