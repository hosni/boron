document.addEventListener("DOMContentLoaded", function () {
  const input = document.querySelector(".md-search__input");

  if (input !== null) {
    input.addEventListener("focus", () => {
      document.dispatchEvent(new CustomEvent("readthedocs-search-show"));
    });
  }
});

document.addEventListener("readthedocs-addons-data-ready", function (event) {
  const config = event.detail.data();
  const versioning = `
<div class="md-version">
  <button class="md-version__current" aria-label="Select version">
    ${config.versions.current.slug}
  </button>
  <ul class="md-version__list">
    ${config.versions.active
      .map(
        (version) => `
    <li class="md-version__item">
      <a href="${version.url}" class="md-version__link">
        ${version.slug}
      </a>
    </li>`
      )
      .join("\n")}
  </ul>
</div>`;

  const currentVersions = document.querySelector(".md-version");
  if (currentVersions !== null) {
    currentVersions.remove();
  }

  const topic = document.querySelector(".md-header__topic");
  if (topic !== null) {
    topic.insertAdjacentHTML("beforeend", versioning);
  }
});
