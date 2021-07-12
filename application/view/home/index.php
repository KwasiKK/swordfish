<div class="row">
    <div class="col-md-12">
        <h3>Logged in as <?php echo $user->name; ?></h3>
        <?php 
            echo '<pre>';
            print_r($user);
            echo '</pre>';
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <button type="button" class="btn btn-success"
            data-bs-toggle="modal" data-bs-target="#addIssueModal">Add Issue</button>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Number</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description(Body)</th>
                    <th scope="col">Client</th>
                    <th scope="col">Priority</th>
                    <th scope="col">Type</th>
                    <th scope="col">Assigned To</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody id="issues">
                <tr class="template-row issue">
                    <th class="number" scope="row"></th>
                    <td class="title"></td>
                    <td class="description"></td>
                    <td class="client"></td>
                    <td class="priority"></td>
                    <td class="type"></td>
                    <td class="assigned_to"></td>
                    <td class="status"></td>
                </tr>    
            </tbody>
        </table>
    </div>
    <div class="col-md-12 feedback"></div>
</div>
<!-- Modal -->
<div class="modal fade" id="addIssueModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form id="addIssueForm">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Issue Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Issue Description (Body)</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Client</label>
                    <select class="form-control" name="client" required>
                        <option></option>
                        <option value="Client ABC">Client ABC</option>
                        <option value="Client XYZ">Client XYZ</option>
                        <option value="Client MNO">Client MNO</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Priority</label>
                    <select class="form-control" name="priority" required>
                        <option></option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Type</label>
                    <select class="form-control" name="type" required>
                        <option></option>
                        <option value="Bug">Bug</option>
                        <option value="Support">Support</option>
                        <option value="Enhancement">Enhancement</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="save-changes">Save changes</button>
                <div class="form-feedback col-md-12"></div>
            </div>
        </form>
    </div>
  </div>
</div>
