@extends('layouts.app')
@section('header-title', __('Bank Accounts'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>
            {{ __('Bank Accounts') }}
        </h4>

        <button type="button" data-bs-toggle="modal" data-bs-target="#bankAccountModal" class="btn py-2 btn-primary">
            <i class="fa fa-plus-circle"></i>
            {{__('Add Bank Account')}}
        </button>

    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-md">
                        <thead>
                            <tr>
                                <th>{{ __('SL') }}</th>
                                <th>{{ __('Primary') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Recipient Name') }}</th>
                                <th>{{ __('Bank Name') }}</th>
                                <th>{{ __('IBAN') }}</th>
                                <th>{{ __('SWIFT/BIC') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($bankAccounts as $key => $bankAccount)
                            @php
                                $serial = $bankAccounts->firstItem() + $key;
                            @endphp
                            <tr>
                                <td>{{ $serial }}</td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input primary-radio" type="radio" 
                                            name="is_primary" 
                                            value="{{ $bankAccount->id }}" 
                                            {{ $bankAccount->is_primary ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $bankAccount->country_name }}</span>
                                </td>
                                <td>{{ $bankAccount->recipient_name }}</td>
                                <td>{{ $bankAccount->bank_name }}</td>
                                <td><code>{{ $bankAccount->iban }}</code></td>
                                <td><code>{{ $bankAccount->swift_bic }}</code></td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info edit-btn" 
                                        data-id="{{ $bankAccount->id }}"
                                        data-country="{{ $bankAccount->country_code }}"
                                        data-recipient="{{ $bankAccount->recipient_name }}"
                                        data-bank="{{ $bankAccount->bank_name }}"
                                        data-iban="{{ $bankAccount->iban }}"
                                        data-swift="{{ $bankAccount->swift_bic }}"
                                        data-purpose="{{ $bankAccount->purpose_of_payment }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editBankAccountModal">
                                        <i class="fa fa-edit"></i> {{__('Edit')}}
                                    </button>
                                    <a href="{{ route('shop.bank-account.destroy', $bankAccount->id) }}"
                                        class="btn btn-sm btn-danger confirm">
                                        <i class="fa fa-trash"></i> {{__('Delete')}}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $bankAccounts->withQueryString()->links() }}
        </div>

    </div>

    <!-- Add Bank Account Modal -->
    <form id="bankAccountForm" method="POST">
        @csrf
        <div class="modal fade" id="bankAccountModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">
                            {{__('Add Bank Account')}}
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Country')}} <span class="text-danger">*</span>
                                </label>
                                <select name="country_code" id="country_code" class="form-control" required>
                                    <option value="">{{__('Select Country')}}</option>
                                    <option value="TR">Turkey</option>
                                    <option value="MK">North Macedonia</option>
                                    <option value="UA">Ukraine</option>
                                    <option value="XK">Kosovo</option>
                                </select>
                                <span class="text-danger" id="country_code-error"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Recipient Name')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="recipient_name" id="recipient_name" class="form-control"
                                    placeholder="{{__('Recipient Name')}}" required>
                                <span class="text-danger" id="recipient_name-error"></span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Bank Name')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control"
                                    placeholder="{{__('Bank Name')}}" required>
                                <span class="text-danger" id="bank_name-error"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('IBAN')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="iban" id="iban" class="form-control"
                                    placeholder="{{__('IBAN')}}" required>
                                <span class="text-danger" id="iban-error"></span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('SWIFT/BIC Code')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="swift_bic" id="swift_bic" class="form-control"
                                    placeholder="{{__('SWIFT/BIC Code')}}" required>
                                <span class="text-danger" id="swift_bic-error"></span>
                            </div>

                            <div class="col-md-6" id="purpose_field" style="display: none;">
                                <label class="form-label">
                                    {{__('Purpose of Payment')}} <span class="text-danger">*</span>
                                    <small class="text-muted">(Required for Ukraine)</small>
                                </label>
                                <input type="text" name="purpose_of_payment" id="purpose_of_payment" class="form-control"
                                    placeholder="{{__('Purpose of Payment')}}">
                                <span class="text-danger" id="purpose_of_payment-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{__('Close')}}
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            {{__('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Bank Account Modal -->
    <form id="editBankAccountForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editBankAccountModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">
                            {{__('Edit Bank Account')}}
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_bank_id" name="bank_id">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Country')}} <span class="text-danger">*</span>
                                </label>
                                <select name="country_code" id="edit_country_code" class="form-control" required>
                                    <option value="">{{__('Select Country')}}</option>
                                    <option value="TR">Turkey</option>
                                    <option value="MK">North Macedonia</option>
                                    <option value="UA">Ukraine</option>
                                    <option value="XK">Kosovo</option>
                                </select>
                                <span class="text-danger" id="edit_country_code-error"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Recipient Name')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="recipient_name" id="edit_recipient_name" class="form-control"
                                    placeholder="{{__('Recipient Name')}}" required>
                                <span class="text-danger" id="edit_recipient_name-error"></span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('Bank Name')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="bank_name" id="edit_bank_name" class="form-control"
                                    placeholder="{{__('Bank Name')}}" required>
                                <span class="text-danger" id="edit_bank_name-error"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('IBAN')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="iban" id="edit_iban" class="form-control"
                                    placeholder="{{__('IBAN')}}" required>
                                <span class="text-danger" id="edit_iban-error"></span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{__('SWIFT/BIC Code')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="swift_bic" id="edit_swift_bic" class="form-control"
                                    placeholder="{{__('SWIFT/BIC Code')}}" required>
                                <span class="text-danger" id="edit_swift_bic-error"></span>
                            </div>

                            <div class="col-md-6" id="edit_purpose_field" style="display: none;">
                                <label class="form-label">
                                    {{__('Purpose of Payment')}} <span class="text-danger">*</span>
                                    <small class="text-muted">(Required for Ukraine)</small>
                                </label>
                                <input type="text" name="purpose_of_payment" id="edit_purpose_of_payment" class="form-control"
                                    placeholder="{{__('Purpose of Payment')}}">
                                <span class="text-danger" id="edit_purpose_of_payment-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{__('Close')}}
                        </button>
                        <button type="submit" id="editSubmitBtn" class="btn btn-primary">
                            {{__('Update') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Show/hide purpose of payment field based on country selection (Add form)
        $('#country_code').on('change', function() {
            if ($(this).val() === 'UA') {
                $('#purpose_field').show();
                $('#purpose_of_payment').prop('required', true);
            } else {
                $('#purpose_field').hide();
                $('#purpose_of_payment').prop('required', false);
                $('#purpose_of_payment').val('');
            }
        });

        // Show/hide purpose of payment field based on country selection (Edit form)
        $('#edit_country_code').on('change', function() {
            if ($(this).val() === 'UA') {
                $('#edit_purpose_field').show();
                $('#edit_purpose_of_payment').prop('required', true);
            } else {
                $('#edit_purpose_field').hide();
                $('#edit_purpose_of_payment').prop('required', false);
                $('#edit_purpose_of_payment').val('');
            }
        });

        // Populate edit modal with data
        $('.edit-btn').on('click', function() {
            const id = $(this).data('id');
            const country = $(this).data('country');
            const recipient = $(this).data('recipient');
            const bank = $(this).data('bank');
            const iban = $(this).data('iban');
            const swift = $(this).data('swift');
            const purpose = $(this).data('purpose');

            $('#edit_bank_id').val(id);
            $('#edit_country_code').val(country).trigger('change');
            $('#edit_recipient_name').val(recipient);
            $('#edit_bank_name').val(bank);
            $('#edit_iban').val(iban);
            $('#edit_swift_bic').val(swift);
            $('#edit_purpose_of_payment').val(purpose);
        });

        // Delete confirmation
        $(".confirm").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "{{__('Are you sure?')}}",
                text: "{{__('This bank account will be deleted permanently!')}}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{__('Yes, Delete it!')}}",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Add bank account form submission
        $('#bankAccountForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $('#submitBtn').prop('disabled', true);
            
            $.ajax({
                url: "{{ route('shop.bank-account.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(response) {
                    $('#submitBtn').prop('disabled', false);
                    const errors = response.responseJSON.errors;
                    
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.text-danger').text('');
                    
                    // Display errors
                    if (errors) {
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            title: response.responseJSON.message,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Ok"
                        });
                    }
                }
            });
        });

        // Edit bank account form submission
        $('#editBankAccountForm').on('submit', function(e) {
            e.preventDefault();
            const bankId = $('#edit_bank_id').val();
            const formData = $(this).serialize();
            $('#editSubmitBtn').prop('disabled', true);
            
            $.ajax({
                url: "{{ url('shop/bank-account') }}/" + bankId + "/update",
                type: "PUT",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(response) {
                    $('#editSubmitBtn').prop('disabled', false);
                    const errors = response.responseJSON.errors;
                    
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.text-danger').text('');
                    
                    // Display errors
                    if (errors) {
                        $.each(errors, function(field, messages) {
                            $('#edit_' + field).addClass('is-invalid');
                            $('#edit_' + field + '-error').text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            title: response.responseJSON.message,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Ok"
                        });
                    }
                }
            });
        });
        // Primary radio button change
        $('.primary-radio').on('change', function() {
            const bankId = $(this).val();
            const url = "{{ url('shop/bank-account') }}/" + bankId + "/make-primary";
            
            $.ajax({
                url: url,
                type: "PUT",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        window.location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: "Error!",
                        text: response.responseJSON.message || "{{__('Something went wrong')}}",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    });
                }
            });
        });

    </script>
@endpush
