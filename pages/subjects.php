<?php include '../includes/header.php'; ?>
<div class="container mt-4">
  <h2>Subject Management</h2>
  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#subjectModal" id="addSubjectBtn">Add Subject</button>
  <div class="table-responsive">
    <table class="table table-bordered" id="subjectsTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<!-- Subject Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="subjectForm">
        <div class="modal-header">
          <h5 class="modal-title" id="subjectModalLabel">Add/Edit Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="subjectId">
          <div class="mb-3">
            <label for="subjectName" class="form-label">Subject Name</label>
            <input type="text" class="form-control" id="subjectName" name="name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/subjects.js"></script>
<?php include '../includes/footer.php'; ?>
