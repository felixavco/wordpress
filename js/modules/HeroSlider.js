import $ from 'jquery';

class HeroSlider {
  constructor() {
    this.els = $(".hero-slider");
    this.initSlider();
  }

  initSlider() {
    this.els.slick({
      autoplay: true,
      arrows: true,
      dots: true
    });
  }
}

export default HeroSlider;