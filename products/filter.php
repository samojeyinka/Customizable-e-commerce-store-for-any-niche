<div class="w-[90%] mx-auto py-2 hidden md:flex items-center gap-5">
    <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Filter</span>

    <div class="flex items-center md:gap-2 lg:gap-4">
        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Sort by</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <div onclick="selectFilterOption(this)">All</div>
                        <div onclick="selectFilterOption(this)">Popularity</div>
                        <div onclick="selectFilterOption(this)">Latest</div>
                        <div onclick="selectFilterOption(this)">Amount: High to Low</div>
                        <div onclick="selectFilterOption(this)">Amount: Low to High</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Color</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Black</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blue</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Red</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Amount</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦50,000 - ₦150,000</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦151,000 - 250,000</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦251,000 - ₦500,000</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦500,000 and above</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Size</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Category</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blankets </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Throws</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Bed Sheets</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Pillowcases</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Duvet Covers</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Comforters </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Mattress Toppers</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Lightning</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Texture</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Soft </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Medium </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Hard </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Rating</span>
                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 star </span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">4 star</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">3 star</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2 star</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">1 star</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-1">
        <img src="../assets/products/round.svg" class="w-[15.63px]" />
        <span class="text-[#EE3F3F] text-[13px] md:text-[14px] font-Onest font-regular">Reset filter</span>
    </div>
</div>


<style>
/* Styles for the filter dropdowns */
.filter-dropdown {
    position: relative;
    cursor: pointer;
}

.filter-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.375rem;
    z-index: 50;
    display: none;
    padding: 8px 12px;
    min-width: 12rem;
}

.filter-dropdown.active .filter-menu {
    display: block;
}

.filter-dropdown.active .arrow-down {
    transform: rotate(180deg);
}
</style>

<script>
// Dropdown functionality with renamed classes
document.querySelectorAll(".filter-dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".filter-toggle");

    toggle.addEventListener("click", function(event) {
        event.stopPropagation();
        document.querySelectorAll(".filter-dropdown").forEach((dd) => {
            if (dd !== dropdown) dd.classList.remove("active");
        });
        dropdown.classList.toggle("active");
    });
});

document.addEventListener("click", function() {
    document.querySelectorAll(".filter-dropdown").forEach((dropdown) => {
        dropdown.classList.remove("active");
    });
});

function selectFilterOption(element) {
    let dropdown = element.closest(".filter-dropdown");
    let toggle = dropdown.querySelector(".filter-toggle span");
    toggle.innerText = element.innerText;
    dropdown.classList.remove("active");
}
</script>