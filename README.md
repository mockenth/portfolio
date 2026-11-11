# Personal Portfolio Website

A responsive, static portfolio for a computer student and graphic designer. Built with HTML, CSS, and vanilla JavaScript. No login, registration, or admin dashboard.

## Run the project

**Quickest option:** extract the ZIP, open the `portfolio` folder, then double-click `index.html`.

**Recommended (VS Code):**
1. Extract the ZIP.
2. Open the `portfolio` folder in Visual Studio Code.
3. Install the **Live Server** extension.
4. Right-click `index.html` and choose **Open with Live Server**.

No npm install, database, or build command is required. An internet connection is needed for the Google Fonts stylesheet; the rest of the site is local.

## Customize your information

Open `assets/js/data.js`. Update:
- `owner.name`, `owner.email`, and `owner.roles`
- `projects` with your real project details, images, technologies, GitHub and demo URLs
- `designs` with your actual graphic design work
- `certificates` with genuine certificate details
- `journey` with your education and experience
- `socials` with your real profile URLs

Search `index.html` for `[OWNER_NAME]`, `[BIOGRAPHY]`, and `[EMAIL_ADDRESS]` and replace those placeholders too.

## Replace images

- Profile photo: replace `assets/images/profile/profile-placeholder.svg` with your photo. If you use a different filename, update the image path in `index.html`.
- Graphic design: put images in `assets/images/graphic-designs/`, then update each `image` path in `assets/js/data.js`.
- Project thumbnails: add an `image` path to each project object.
- Certificates: add a genuine certificate image path in `image`. For a PDF, place it in `assets/documents/certificates/` and set `file` to that relative path.

Use compressed JPG, PNG, or WebP images. Keep original certificate files safe.

## CV download

Place your CV PDF at `assets/documents/cv.pdf`, then add a link in the hero section of `index.html`, for example:
`<a class="btn btn-ghost" href="assets/documents/cv.pdf" download>Download my CV ↧</a>`

The CV link is intentionally not enabled until you provide your own CV.

## Contact form

The form validates the fields and opens the visitor's default email application using a `mailto:` link. Set the real email in `assets/js/data.js` and in the email links in `index.html`.

**Important:** A frontend-only HTML/CSS/JavaScript site cannot securely send or store messages on its own. For automatic delivery without relying on the visitor's email app, connect a backend (for example PHP + PHPMailer/SMTP) or a form service such as Formspree. Never put SMTP passwords or private API keys in frontend JavaScript.

## Theme and accessibility

- Dark mode is the default; the theme toggle switches to light mode and stores the choice in localStorage.
- Navigation, project filters, design filters, lightbox controls, keyboard Escape/arrow navigation, and responsive mobile menu are implemented.
- Reduced-motion preferences are respected.

## Important

The included portfolio entries and SVG artwork are editable placeholders, not claims about real projects, credentials, or finished client work. Replace them with your own verified information before publishing.
