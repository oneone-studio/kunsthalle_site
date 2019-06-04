<!-- header start -->
<div id="header">
    <div class="center">
        <div id="menu-wrapper">
            <div id="logo-wrapper">
                <a href="<?php echo SITE_DOMAIN;?>/index"><img class="logo" src="/images/kunsthalle_bremen_logo.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
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
                <?php 
                    // $link = '/'.$lang.'/'.strtolower(str_replace(' ', '-', $mi->title_en));
                    $link = '/'.$lang.'/'.$mi->{'slug_'.$lang};
                    if(strtolower($mi->{'title_'.$lang}) == 'besuch planen' || $mi->{'slug_'.$lang} == 'besuch-planen') { $link .= '/ihr-besuch'; }
                    if(strtolower($mi->{'title_'.$lang}) == 'plan a visit' || $mi->{'slug_'.$lang} == 'plan-a-visit'
                         || $mi->{'slug_'.$lang} == 'plan-your-visit') { $link .= '/your-visit'; }
                    if(strtolower($mi->title_en) == 'exhibitions') { $link = '/'.$lang.'/view/exhibitions/list/current'; }
                    if(strtolower($mi->title_en) == 'blog') { $link = 'http://kunsthallebremen.tumblr.com'; }

                    $link_title = $mi->{'title_'.$lang};
                ?>
                <li><a href="{{$link}}">{{ $link_title }}</a></li>
              @endforeach  
            </ul>
        </div>
    </div>
    <div class="left">
        @if($lang == 'de')
            <a href="/{{$lang}}/besuch-planen/ihr-besuch" title="Ihr Besuch">
                <span class="icon icon-visit-us icon-is"></span>
                <span class="hidden-xs">
                    Ihr Besuch
                </span>
            </a>
            <a href="/{{$lang}}/besuch-planen/kalender" title="Besuch Planen">
                <span class="icon icon-calendar icon-is"></span>
                <span class="hidden-xs">
                    VERANSTALTUNGEN
                </span>
            </a>
        @endif
        @if($lang == 'en')
            <a href="/{{$lang}}/plan-your-visit/your-visit" title="Ihr Besuch">
                <span class="icon icon-visit-us icon-is"></span>
                <span class="hidden-xs">
                    Your Visit
                </span>
            </a>
        @endif
            
    </div>
    <div class="right">
        <span class="link search">
            <a href="#">
                <span class="icon icon-search"></span>
            </a>
        </span>
        <?php if($lang == 'de'): ?>   
                <span class="link language text-white">   
                    <a href="/{{$lang}}/kh/top/main/set-lang?lang=en&uri=<?php echo $_SERVER['REQUEST_URI'];?>" title="English">en</a>
                </span>
                <span class="link tickets text-white">
                    <a href="https://www.mus-ticket.de/new/app/Shopping?ref=shp157393406&n=KHBremen" title="Buy Tickets" target="_blank">Tickets</a>
                </span>
        <?php endif; ?>
        <?php if($lang == 'en'): ?>
                <span class="link language text-white">
                    <a href="/{{$lang}}/kh/top/main/set-lang?lang=de&uri=<?php echo $_SERVER['REQUEST_URI'];?>" title="Deutsche">de</a>
                </span>
                <span class="link tickets text-white">
                    <a href="https://www.mus-ticket.de/new/app/Shopping?ref=shp157393406&n=KHBremen" title="Tickets kaufen" target="_blank">Tickets</a>
                </span>
        <?php endif; ?>
    </div>
</div>
<!-- header ende -->
