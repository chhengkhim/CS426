<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/register.css') }}" rel="stylesheet">
</head>
<body>

  <div class="register-page-main">
    <div class="register-container">
      <h1 class="register-main-title">Sign Up</h1>

      @if ($errors->any())
        <div class="register-alert register-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="registerForm" method="POST" enctype="multipart/form-data" class="register-form">
        @csrf

        <!-- Role selection -->
        <div class="form-group">
          <label for="role" class="form-label">Select Role:</label>
          <select id="role" name="role" class="form-select" required>
            <option value="">-- Choose Role --</option>
            <option value="customer">Customer</option>
            <option value="seller">Seller</option>
          </select>
        </div>

        <!-- Seller Image Input -->
        <div id="sellerImageDiv" class="form-group" style="display: none;">
          <label for="seller_profile_img" class="form-label">Seller Profile Picture:</label>
          <input type="file" id="seller_profile_img" name="seller_profile_img" class="form-control-file"/>
        </div>

        <!-- Customer Image Input -->
        <div id="customerImageDiv" class="form-group" style="display: none;">
          <label for="customer_profile_images" class="form-label">Customer Profile Picture:</label>
          <input type="file" id="customer_profile_img" name="customer_profile_images" class="form-control-file"/>
        </div>

        <!-- Common Inputs -->
        <div class="form-group">
          <label for="full_name" class="form-label">Full Name:</label>
          <input type="text" id="full_name" name="full_name" class="form-control" required />
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email:</label>
          <input type="email" id="email" name="email" class="form-control" required />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password:</label>
          <input type="password" id="password" name="password" class="form-control" required />
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm Password:</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required />
        </div>

        <!-- Seller Fields -->
        <div id="sellerFields" class="seller-fields-section" style="display: none;">
          <h3 class="section-subtitle">Seller Details</h3>
          <div class="form-group">
            <label for="store_name" class="form-label">Store Name:</label>
            <input type="text" id="store_name" name="store_name" class="form-control" />
          </div>

          <div class="form-group">
            <label for="address_line" class="form-label">Address:</label>
            <input type="text" id="address_line" name="address_line" class="form-control" >
          </div>
          <div class="form-row">
            <div class="form-group-half">
              <label for="city" class="form-label">City:</label>
              <input type="text" id="city" name="city" class="form-control" >
            </div>
            <div class="form-group-half">
              <label for="state" class="form-label">State:</label>
              <input type="text" id="state" name="state" class="form-control" >
            </div>
          </div>
          <div class="form-group">
            <label for="zip" class="form-label">Zip Code:</label>
            <input type="text" id="zip" name="zip" class="form-control" >
          </div>
        </div>

        <!-- Phone (common) -->
        <div class="form-group">
          <label for="phone_number" class="form-label">Phone Number:</label>
          <input type="text" id="phone_number" name="phone_number" class="form-control" required/>
        </div>

        <!-- Customer Fields -->
        <div id="customerFields" class="customer-fields-section" style="display: none;">
          <h3 class="section-subtitle">Customer Details</h3>
          <div class="form-group">
            <label for="age" class="form-label">Age:</label>
            <input type="number" id="age" name="age" class="form-control" />
          </div>

          <div class="form-group">
            <label class="form-label">Gender:</label>
            <div class="radio-group">
              <input type="radio" name="gender" value="male" id="gender_male" class="radio-input"/>
              <label for="gender_male" class="radio-label">Male</label>
            </div>
            <div class="radio-group">
              <input type="radio" name="gender" value="female" id="gender_female" class="radio-input"/>
              <label for="gender_female" class="radio-label">Female</label>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="register-btn">Sign Up</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('registerForm');
      const roleSelect = document.getElementById('role');

      const sellerFields = document.getElementById('sellerFields');
      const customerFields = document.getElementById('customerFields');
      const sellerImageInput = document.getElementById('seller_profile_img');
      const customerImageInput = document.getElementById('customer_profile_img');
      const sellerImageDiv = document.getElementById('sellerImageDiv');
      const customerImageDiv = document.getElementById('customerImageDiv');

      const emailInput = document.getElementById('email');
      const ageInput = document.getElementById('age');
      const genderRadios = document.querySelectorAll('input[name="gender"]');
      const storeNameInput = document.getElementById('store_name');
      const sellerAddressLineInput = document.getElementById('address_line');
      const sellerCityInput = document.getElementById('city');
      const sellerStateInput = document.getElementById('state');
      const sellerZipInput = document.getElementById('zip');

      function updateFormBasedOnRole() {
        const role = roleSelect.value;

        // Hide all conditional sections and reset required attributes
        sellerFields.style.display = 'none';
        customerFields.style.display = 'none';
        sellerImageDiv.style.display = 'none';
        customerImageDiv.style.display = 'none';

        sellerImageInput.removeAttribute('required');
        customerImageInput.removeAttribute('required');
        storeNameInput.removeAttribute('required');
        sellerAddressLineInput.removeAttribute('required');
        sellerCityInput.removeAttribute('required');
        sellerStateInput.removeAttribute('required');
        sellerZipInput.removeAttribute('required');
        ageInput.removeAttribute('required');
        genderRadios.forEach(radio => radio.removeAttribute('required'));

        // Set initial name attributes for email inputs
        // The original HTML had 'email' and 'customers_email' / 'seller_email' handled by JS, so keep this logic.
        emailInput.name = 'email'; // Default to a common name first

        if (role === 'seller') {
          form.action = '/process_Registers_seller';
          sellerFields.style.display = 'block';
          sellerImageDiv.style.display = 'block';

          sellerImageInput.setAttribute('required', 'required');
          storeNameInput.setAttribute('required', 'required');
          sellerAddressLineInput.setAttribute('required', 'required');
          sellerCityInput.setAttribute('required', 'required');
          sellerStateInput.setAttribute('required', 'required');
          sellerZipInput.setAttribute('required', 'required');

          emailInput.name = 'seller_email';
        } else if (role === 'customer') {
          form.action = '/process_registers_customer';
          customerFields.style.display = 'block';
          customerImageDiv.style.display = 'block';

          customerImageInput.setAttribute('required', 'required');
          ageInput.setAttribute('required', 'required');
          genderRadios.forEach(radio => radio.setAttribute('required', 'required'));

          emailInput.name = 'customers_email';
        } else {
          // Reset form action if no role is selected (e.g., default option)
          form.action = '#'; // Or a default action that handles no selection
        }
      }

      roleSelect.addEventListener('change', updateFormBasedOnRole);

      // Call once on page load to set initial state based on default select option
      updateFormBasedOnRole();

      // This submit listener seems to be redundant with updateFormBasedOnRole
      // as role-based action is set on change. Removed for clarity, assuming client-side validation logic.
      // form.addEventListener('submit', updateFormBasedOnRole);
    });
  </script>
</body>
</html>
