     <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
         <div class="app-brand demo">
             <a href="index.html" class="app-brand-link">
                 <span class="app-brand-logo demo">
                     <span class="text-primary">
                         <img src="{{ asset('images/logo_small.png') }}" class="w-px-50 h-auto" alt=""
                             srcset="">
                     </span>
                 </span>
                 <span class="app-brand-text demo menu-text fw-bold ms-2">Rabbiroots</span>
             </a>

             <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                 <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
             </a>
         </div>

         <div class="menu-divider mt-0"></div>

         <div class="menu-inner-shadow"></div>

         <ul class="menu-inner py-1">
             <!-- Dashboards -->
             <li class="menu-item {{ request()->is('/') ? 'active open' : '' }}">
                 <a href="/" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-home-smile"></i>
                     <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
                     {{-- <span class="badge rounded-pill bg-danger ms-auto">5</span> --}}
                 </a>
             </li>
             <li
                 class="menu-item {{ request()->is('category*') || request()->is('subcategory*') ? 'active open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons bx bx-store"></i>
                     <div class="text-truncate" data-i18n="Catalog">Catalog</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is('category*') ? 'active' : '' }}">
                         <a href="{{ route('category.index') }}" class="menu-link">
                             <div class="text-truncate" data-i18n="Products">
                                 <i class="bx bx-category me-1"></i> Category
                             </div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is('subcategory*') ? 'active' : '' }}">
                         <a href="{{ route('subcategory.index') }}" class="menu-link">
                             <div class="text-truncate" data-i18n="Products">
                                 <i class="bx bx-sitemap me-1"></i> Subcategory
                             </div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is('product*') ? 'active' : '' }}">
                         <a href="{{ route('product.index') }}" class="menu-link">
                             <div class="text-truncate" data-i18n="Products">
                                 <i class="bx bx-package me-1"></i> Product
                             </div>
                         </a>
                     </li>
                 </ul>
             </li>
        </ul>
     </aside>
