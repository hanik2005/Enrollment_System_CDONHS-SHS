document.addEventListener("DOMContentLoaded", function(){

const provinceSelect = document.getElementById("provinceSelect");
const citySelect = document.getElementById("citySelect");
const barangaySelect = document.getElementById("barangaySelect");

const API = "https://psgc.gitlab.io/api";

// LOAD PROVINCES
fetch(API + "/provinces/")
.then(res => res.json())
.then(data => {

data.forEach(province => {

let option = document.createElement("option");
option.value = province.code;
option.textContent = province.name;

provinceSelect.appendChild(option);

});

});

// PROVINCE → CITIES + MUNICIPALITIES
provinceSelect.addEventListener("change", function(){

citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

if(!this.value) return;

Promise.all([
fetch(API + "/provinces/" + this.value + "/cities/").then(res => res.json()),
fetch(API + "/provinces/" + this.value + "/municipalities/").then(res => res.json())
])
.then(([cities, municipalities]) => {

[...cities, ...municipalities].forEach(place => {

let option = document.createElement("option");
option.value = place.code;
option.textContent = place.name;

citySelect.appendChild(option);

});

});

});

// CITY / MUNICIPALITY → BARANGAYS
citySelect.addEventListener("change", function(){

barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

if(!this.value) return;

fetch(API + "/cities/" + this.value + "/barangays/")
.then(res => res.json())
.then(data => {

data.forEach(barangay => {

let option = document.createElement("option");
option.value = barangay.name;
option.textContent = barangay.name;

barangaySelect.appendChild(option);

});

});

});

});
