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

## 🔄️ Workflow
 1. Upload sales / inventory to upload module
 2. Generate File
 3. Download Batch
 4. Tag as final

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