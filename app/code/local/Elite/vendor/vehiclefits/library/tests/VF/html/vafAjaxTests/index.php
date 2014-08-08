<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
      "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
        <style type="text/css">
            a{
                display: block;
            }
        </style>
        <script src="/skin/adminhtml/default/default/jquery-1.7.1.min.js"> </script>
        <script type="text/javascript">
            var testPages = new Array(
                'Installer.php',
                'ajaxTestJs/MMY.php',
                'ajaxTestJs/Space.php',
                'ajaxTestJs/MMYLoadNextLevelAuto.php',
                'ajaxTestJs/MMYNonAjax.php',
                'ajaxTestJs/MMTC.php',
                'ajaxTestJs/MMYdisabled.php',
                'ajaxTestJs/MMYhide.php',
                'ajaxTestJs/MMYInUse.php',
                'chooserTest/MMY.php',
                'multiTreeTest/MMY.php',
                'multiTreeTest/MMYHide.php',
                'multiTreeTest/MMYMultiple.php',
                'multiTreeTest/PreExistingFits.php'
            );
            
            var currentPage = 0; 
            
            var iframeForLink = function( link ) {
                return $(link).next('iframe');
            }
               
            var toggleIframe = function( link ) {
                iframeForLink( link ).toggle();
            }
            
            var bindToggles = function() {
                $('.pageFrameToggle').unbind('click');
                $('.pageFrameToggle').click( function() {
                    toggleIframe(this);
                });
            }
            
            var loadPage = function(page) { 
                var pageName = testPages[page];
                $('#testPageContainer').append( '<a href="#" class="pageFrameToggle page' + page + '">' + pageName + '</a>' );
                bindToggles();
                $('#testPageContainer').append( '<iframe src="' + pageName + '" width="500" height="500"></iframe>' );
            }
            
            var areMorePages = function() {
                return currentPage < testPages.length;
            }
            
            var runCurrentPage = function() {
                loadPage( currentPage );
                currentPage++;
            }
            
            var getPageIndex = function( pageName ) {
                var i = 0;
                while( i < testPages.length ) {
                    if( pageName == testPages[i] ) {
                        return i;
                    }
                    i++;
                }
                return false;
            }
            
            var testPageComplete = function( testPage, failures, total ) {
                var result = (failures == 0 ? '' : failures + ' failures, ') + total + ' Tests Total<br />';
                var selector = '.page' + getPageIndex( testPage );
                var status = failures > 0 ? 'fail' : 'pass';
                $(selector).append( ' <span class="' + status + '">' + result + '</span>' ).click();
                if( areMorePages() ) {
                    runCurrentPage();
                }
            }  
            
            var runSuite = function() {
                runCurrentPage();
            }
            $(document).ready( runSuite );
            
        </script>
        <style type="text/css">
            #testPageContainer {
                width:500px;
            }
            .pass {
                float:right;
                background-color:green;
                color:white;
            }
            .fail {
                float:right;
                background-color:red;
                color:white;
            }
        </style>
  </head>
<body>
    <div id="testPageContainer"></div>
</body>
</html>