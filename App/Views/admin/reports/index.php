<?php
use App\Core\App;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Generate Reports</h2>

<form action="/admin/reports/generate" method="get">
  <div>
    <label for="start_date">Start Date</label>
    <input type="date" id="start_date" name="start_date" value="<?= date('Y-m-01') ?>" required>
  </div>

  <div>
    <label for="end_date">End Date</label>
    <input type="date" id="end_date" name="end_date" value="<?= date('Y-m-d') ?>" required>
  </div>

  <button type="submit">Generate Report</button>
</form>

<p><a href="/admin">Back to Admin Dashboard</a></p>
