<!-- header start -->
<div id="header">
    <div class="center">
        <div id="menu-wrapper">
            <div id="logo-wrapper">
                <a href="http://kunsthalle-bremen.de/index"><img class="logo" src="/images/kunsthalle_bremen_logo.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
            </div>
            <div class="opener">
                <a href="javascript:kunsthalle.closeMenu()" class="opener-close-link">
                    <span class="icon icon-up"></span>
                </a>
                <a href="javascript:kunsthalle.openMenu()" class="opener-open-link">
                    <span class="icon icon-down"></span>
                </a>
            </div>
            <div>
                <span class="link"><a href="javascript:kunsthalle.toggleMenu()">Menu</a></span>
            </div>
            <ul class="list-unstyled text-white">
              @foreach($menu as $mi)
                <?php $link = '/'.strtolower(str_replace(' ', '-', $mi->title_en));
                    if(strtolower($mi->title_en) == 'exhibitions') { $link = '/view/exhibitions/list/current'; }
                    if(strtolower($mi->title_en) == 'blog') { $link = 'http://kunsthallebremen.tumblr.com'; }
                ?>
                <li><a href="{{$link}}">{{$mi->title_de}}</a></li>
              @endforeach  
            </ul>
        </div>
    </div>
    <div class="left">
        <a href="/besuch-planen/ihr-besuch" title="Ihr Besuch">
            <span class="icon icon-visit-us icon-is"></span>
            <span class="hidden-xs">
                Ihr Besuch
            </span>
        </a>
        <a href="/calendar/besuch-planen" title="Besuch Planen">
            <span class="icon icon-calendar icon-is"></span>
            <span class="hidden-xs">
                VERANSTALTUNGEN
            </span>
        </a>
    </div>
    <div class="right">
        <span class="link search">
            <a href="#">
                <span class="icon icon-search"></span>
        </a>
        </span>
        <span class="link tickets text-white">
            <a href="https://www.mus-ticket.de/new/app/Shopping?ref=shp157393406&n=KHBremen" title="Tickets kaufen" target="_blank">Tickets</a>
        </span>
        <span class="link language text-white"><a href="#">DE</a></span>
    </div>
</div>
<!-- header ende -->
