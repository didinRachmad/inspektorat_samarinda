<section>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Update Password') }}</h5>
            <p class="small text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
            <form method="post" action="{{ route('password.update') }}" class="mt-4">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label class="form-label" for="update_password_current_password"
                        class="form-label">{{ __('Current Password') }}</label>
                    <input id="update_password_current_password" name="current_password" type="password"
                        class="form-control" autocomplete="current-password">
                    @error('current_password')
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="update_password_password"
                        class="form-label">{{ __('New Password') }}</label>
                    <input id="update_password_password" name="password" type="password" class="form-control"
                        autocomplete="new-password">
                    @error('password')
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="update_password_password_confirmation"
                        class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                        class="form-control" autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @if (session('status') == 'password-updated')
                    <span class="small text-success ms-3">{{ __('Saved.') }}</span>
                @endif
            </form>
        </div>
    </div>
</section>
