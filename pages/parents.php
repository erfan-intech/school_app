<?php include '../includes/header.php'; ?>
<div class="container mt-4">
  <h2>Parent Management</h2>
  <div class="d-flex align-items-center mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#parentModal" id="addParentBtn">Add Parent</button>
    <input type="text" id="parentSearch" class="form-control w-auto ms-auto" placeholder="Search parents...">
  </div>
  <div class="table-responsive">
    <table class="table table-bordered" id="parentsTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Profile Picture</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Gender</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Address</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<!-- Parent Modal -->
<div class="modal fade" id="parentModal" tabindex="-1" aria-labelledby="parentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="parentForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="parentModalLabel">Add/Edit Parent</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="parentId">
          <div class="mb-3">
            <label for="parentFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="parentFirstName" name="first_name" required>
          </div>
          <div class="mb-3">
            <label for="parentLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="parentLastName" name="last_name">
          </div>
          <div class="mb-3">
            <label for="parentGender" class="form-label">Gender</label>
            <select class="form-select" id="parentGender" name="gender" required>
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="parentPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="parentPhone" name="phone">
          </div>
          <div class="mb-3">
            <label for="parentEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="parentEmail" name="email">
          </div>
          <div class="mb-3">
            <label for="parentAddress" class="form-label">Address</label>
            <input type="text" class="form-control" id="parentAddress" name="address">
          </div>
          <div class="mb-3">
            <label for="parentProfilePicture" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="parentProfilePicture" name="profile_picture" accept="image/*">
            <div id="parentPicPreview" class="mt-2"></div>
            <button type="button" class="btn btn-sm btn-danger mt-2" id="removeParentPhotoBtn" style="display:none;">Remove Photo</button>
            <input type="hidden" name="remove_photo" id="removeParentPhoto" value="0">
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
<script src="../assets/js/parents.js"></script>
<?php include '../includes/footer.php'; ?>
