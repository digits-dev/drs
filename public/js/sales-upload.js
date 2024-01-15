function updateProgress() {
    const importProgresses = $('.import-progress').get();
    importProgresses.forEach(async (importProgress) => {
        const progress = $(importProgress);
        const batchId = progress.attr('id');
        if (!batchId) return;
        const progressBar = progress.find('.import-progress-bar');
        const progressText = progress.find('.import-progress-text');
        const value = progressBar.val();
        if (value == 100) return;
        const response = await fetch(`${window.location}/batch/${batchId}`);
        const data = await response.json();
        const rowCount = data.upload_details.row_count;
        const currentCount = data.count;
        const percent = Math.floor(currentCount / rowCount * 100);
        progressBar.attr('value', percent);
        progressText.text(`${percent}% (${currentCount.toLocaleString()} of ${rowCount.toLocaleString()} rows imported)`);
    });
}
updateProgress();
setInterval(updateProgress, 3000);