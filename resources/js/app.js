// Vue
window.Vue = require("vue");

// Imports
import lazysizes from "lazysizes";
import select_dropdown from "./components/SelectDropdown";

// Vue App
const app = new Vue({
  el: "#app",

  components: {
    select_dropdown
  }
});
