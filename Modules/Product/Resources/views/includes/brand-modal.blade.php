<div class="modal fade" id="brandCreateModal" tabindex="-1" role="dialog" aria-labelledby="brandCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandCreateModalLabel">Create Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('product-brands.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold" for="brand_code">Brand Code <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="brand_code" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" for="brand_name">Brand Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="brand_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
