SELECT
    store_sales.id,
    0 as is_apple,
    store_sales.channels_id,
    store_sales.customers_id,
    customers.concepts_id,
    store_sales.employees_id,
    store_sales.sales_date AS sales_date,
    non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk,
    non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff,
    CONCAT(
        EXTRACT(
            YEAR
            FROM store_sales.sales_date
        ), '_', LPAD(
            EXTRACT(
                MONTH
                FROM store_sales.sales_date
            ), 2, 0
        )
    ) AS sales_date_yr_mo,
    EXTRACT(
        YEAR
        FROM store_sales.sales_date
    ) AS sales_year,
    LPAD(
        EXTRACT(
            MONTH
            FROM store_sales.sales_date
        ), 2, 0
    ) AS sales_month,
    store_sales.item_code AS item_code,
    COALESCE(
        store_sales.digits_code_rr_ref, store_sales.item_code
    ) AS digits_code_rr_ref,
    gacha_items.digits_code AS digits_code,
    store_sales.quantity_sold AS quantity_sold
FROM
    store_sales
    LEFT JOIN systems ON store_sales.systems_id = systems.id
    LEFT JOIN organizations ON store_sales.organizations_id = organizations.id
    LEFT JOIN report_types ON store_sales.report_types_id = report_types.id
    LEFT JOIN channels ON store_sales.channels_id = channels.id
    LEFT JOIN customers ON store_sales.customers_id = customers.id
    LEFT JOIN concepts ON customers.concepts_id = concepts.id
    LEFT JOIN employees ON store_sales.employees_id = employees.id
    LEFT JOIN non_apple_cutoffs ON store_sales.sales_date = non_apple_cutoffs.sold_date
    LEFT JOIN gacha_items ON store_sales.item_code = gacha_items.digits_code
WHERE
    store_sales.is_final = 1
    AND gacha_items.digits_code IS NOT NULL;