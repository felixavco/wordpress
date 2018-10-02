import $ from "jquery";

class Search {
  constructor() {
    this.addSearchHTML();
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.resultDiv = $("#search-overlay__results");
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keydown", this.keyPressDispacher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  typingLogic() {
    //this prevents activate the search if the text is not changed for example when using arrow keys
    let currentVal = this.searchField.val();
    if (currentVal != this.previousValue) {
      clearTimeout(this.typingTimer);
    //If the search field is empty remove the content of the result div and hide the spinner
      if (currentVal) {
        if (!this.isSpinnerVisible) {
          this.resultDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getReults.bind(this), 750);
      } else {
        this.resultDiv.html("");
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  getReults() {
    $.getJSON(`${universityData.root_url}/wp-json/university/v1/search?term=${this.searchField.val()}`, 
      results => {
        this.resultDiv.html(`
          <div class="row">
            <div class="one-third">
              <h2 class="search-overlay__section-title">General Information</h2>
              ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No General Information match your search</p>'}
              ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.authorName ? `<small>by ${item.authorName}</small>`: ''}</li>`).join('')}
              ${results.generalInfo.length ? '</ul>' : ''}
            </div>

            <div class="one-third">
              <h2 class="search-overlay__section-title">Programs</h2>
              ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No Programs match your search <a href="${universityData.root_url}/programs">View all Programs</a></p>`}
              ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
              ${results.programs.length ? '</ul>' : ''}

              <h2 class="search-overlay__section-title">Professors</h2>
              ${results.professors.length ? '<ul class="professor-cards">' : `<p>No Professors match your search</p>`}
              ${results.professors.map(item => `
                <li class="professor-card__list-item">
                  <a class="professor-card" href="${item.permalink}">
                    <img class="professor-card__image" src="${item.image}" alt="${item.title}">
                    <span class="professor-card__name">${item.title}</span>
                  </a>
                </li>
              `).join('')}
              ${results.professors.length ? '</ul>' : ''}

            </div>

            <div class="one-third">
              <h2 class="search-overlay__section-title">Campuses</h2>
              ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No Campuses match your search <a href="${universityData.root_url}/campuses">View all Campuses</a></p>`}
              ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
              ${results.campuses.length ? '</ul>' : ''}

              <h2 class="search-overlay__section-title">Events</h2>
              ${results.events.length ? '' : `<p>No Events match your search <a href="${universityData.root_url}/events">View all Events</a></p>`}
              ${results.events.map(item => `
                <div class="event-summary">
                  <a class="event-summary__date t-center" href="${item.permalink}">
                    <span class="event-summary__month">${item.month}</span>
                    <span class="event-summary__day">${item.day}</span>  
                  </a>
                  <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                    <p>${item.description}<br><a href="${item.permalink}" class="nu gray"> Learn more</a></p>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        `);
        this.isSpinnerVisible = false;
      }
    );

  }

  keyPressDispacher(e) {
    let key = e.keyCode;
    //Evaluates if any input or textarea is focus to prevent open the searh when typing
    let isFocus = $('input, textarea').is(':focus'); 
    if (key == 83 && !this.isOverlayOpen && !isFocus) this.openOverlay();
    if (key == 27 && this.isOverlayOpen) this.closeOverlay();
  }

  openOverlay(e) {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.searchField.val('');
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
    return false
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  addSearchHTML(){
    $('body').append(`
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" id="search-term" class="search-term" placeholder="What are you looking for?">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
        <div class="container">
          <div id="search-overlay__results"></div>
        </div>
      </div>
    `);
  }

}

export default Search;
