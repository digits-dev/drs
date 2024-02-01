SELECT
    digits_code_rr_ref,
    brand_description = 'APPLE' as is_apple,
    sum(quantity_sold) as quantity_sold,
    channel_name,
    customer_location,
    store_concept_name,
    sales_date,
    apple_yr_qtr_wk,
    apple_week_cutoff,
    non_apple_yr_mon_wk,
    non_apple_week_cutoff,
    sales_date_yr_mo,
    sales_year,
    sales_month
from store_sales_report
where exists (
    select digits_code from items where items.digits_code = digits_code_rr_ref
)
and is_final = 1
group by digits_code_rr_ref,
    is_apple,
    channel_name,
    customer_location,
    store_concept_name,
    sales_date,
    apple_yr_qtr_wk,
    apple_week_cutoff,
    non_apple_yr_mon_wk,
    non_apple_week_cutoff,
    sales_date_yr_mo,
    sales_year,
    sales_month;
    