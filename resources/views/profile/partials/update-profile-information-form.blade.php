<section><!-- Profile Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Profile Information') }}</h5>
            <p class="small text-muted">{{ __("Update your account's profile information and email address.") }}</p>
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>
            <form method="post" action="{{ route('profile.update') }}" class="mt-4">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label class="form-label" for="name" class="form-label">{{ __('Name') }}</label>
                    <input id="name" name="name" type="text" class="form-control"
                        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name')
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email" class="form-label">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" class="form-control"
                        value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                        <div class="small text-danger">{{ $message }}</div>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <p class="small text-muted mt-2">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification"
                                class="btn btn-link p-0">{{ __('Click here to re-send the verification email.') }}</button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <div class="small text-success">
                                {{ __('A new verification link has been sent to your email address.') }}</div>
                        @endif
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @if (session('status') === 'profile-updated')
                    <span class="small text-success ms-3">{{ __('Saved.') }}</span>
                @endif
            </form>
        </div>
    </div>
</section>
