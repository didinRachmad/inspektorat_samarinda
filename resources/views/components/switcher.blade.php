<!-- Button Trigger -->
<button
    class="btn btn-primary position-fixed end-0 top-50 d-flex flex-column align-items-center shadow py-2 rounded-start-4 p-1"
    type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" style="z-index: 1044;">

    <i class="material-icons-outlined fs-5">tune</i>
</button>


<!-- Offcanvas Content -->
<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop" style="width: 300px;">
    <div class="offcanvas-header border-bottom h-70">
        <div class="">
            <h5 class="mb-0">Theme Customizer</h5>
            <p class="mb-0">Customize your theme</p>
        </div>
        <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
            <i class="material-icons-outlined">close</i>
        </a>
    </div>
    <div class="offcanvas-body">
        <div>
            <p>Theme variation</p>

            <div class="d-flex flex-column gap-3">
                <div>
                    <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked />
                    <label class="form-label" 
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-start gap-3 p-3 w-100"
                        for="BlueTheme">
                        <span class="material-icons-outlined">contactless</span>
                        <span>Blue Theme</span>
                    </label>
                </div>

                <div>
                    <input type="radio" class="btn-check" name="theme-options" id="LightTheme" />
                    <label class="form-label" 
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-start gap-3 p-3 w-100"
                        for="LightTheme">
                        <span class="material-icons-outlined">light_mode</span>
                        <span>Light Theme</span>
                    </label>
                </div>

                <div>
                    <input type="radio" class="btn-check" name="theme-options" id="DarkTheme" />
                    <label class="form-label" 
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-start gap-3 p-3 w-100"
                        for="DarkTheme">
                        <span class="material-icons-outlined">dark_mode</span>
                        <span>Dark Theme</span>
                    </label>
                </div>

                <div>
                    <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme" />
                    <label class="form-label" 
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-start gap-3 p-3 w-100"
                        for="SemiDarkTheme">
                        <span class="material-icons-outlined">contrast</span>
                        <span>Semi Dark</span>
                    </label>
                </div>

                <div>
                    <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme" />
                    <label class="form-label" 
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-start gap-3 p-3 w-100"
                        for="BoderedTheme">
                        <span class="material-icons-outlined">border_style</span>
                        <span>Bordered Theme</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
