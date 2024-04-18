# Digits Report System

## 📃 Modules
 - Run Rate
 - Digits Sales
 - Store Sales
 - Store Inventory
 - Warehouse Inventory
 - Uploads
    - Digits Sales Upload
    - Store Sales Upload
    - Store Inventory Upload
    - Warehouse Inventory Upload
- Submaster
   - Channel
   - Concept
   - Customer
   - Employee
   - Inventory Type
   - Organization
   - Report Privileges
   - Report Type
   - System

## 🔄️ Workflow
 1. Upload sales / inventory to upload module
 2. Generate File
 3. Download Batch
 4. Tag as final

 ## 📝 Notes:
   - ### Process of Uploading
      1. Upload excel with corresponding date range.
      2. System checks the headers.
      3. System dispatches a job that will chunk the excel and turn each chunk into smaller json arrays (length of 1000 rows) and then inserted to the db as lines. 
      4. System dispatches a job that will insert the json array into the db table respectively as records. If any error occurs while importing or chunking, the process should stop.

   - ### Others
      - Make sure .env file `QUEUE_CONNECTION` variable is set to `database`
      - For some reason, sometimes the `php artisan queue:work` command stops. A custom artisan command is used to check whether the queue has stopped or not. Check file `app\Console\Commands\QueueWorkerChecker.php`. This ensures that the command `php artisan queue:work` is re-run in such occurence.
      - Access to columns in reports per privilege should be set in Report Privileges Module.

   - ### Need to be set up
      - API that will sync the data of submasters from IMFS including initial_wrr_date.
         - admin_items
         - gacha_items
         - items
         - rma_items
         - service_items

## 📃 Upload Statuses
 - 🟠 `FILE UPLOADED`
    - File uploaded successfully
    - File is being processed to chunking into smaller json array
 - 🔵 `IMPORTING`
    - Rows of chunks is being inserted to the database
 - 🔵 `IMPORTING FINISHED`
    - Rows of chunks has been inserted to the database
 - 🟠 `GENERATING FILE`
    - Batch report file is being generated into csv
 - 🔵 `FILE GENERATED`
    - Batch report file has been generated into csv
 - 🔵 `FILE DOWNLOADED`
    - Batch file has been downloaded
 - 🟢 `FINAL`
    - Batch upload has been tagged as final
    - Rows are added to main module (Digits Sales, Store Sales, Store Inventory, Warehouse Inventory)
 - 🔴 `IMPORT FAILED`
    - Importing / inserting of chunks has failed
    - See the failure reason by clicking the eye button
 - 🔴 `FAILED TO GENERATE FILE`
    - Generating batch report file has failed
    - See the failure reason by clicking the eye button
    - Can be tried again by clicking the generate button again.