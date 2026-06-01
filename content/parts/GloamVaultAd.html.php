<div class="ad-banner">
	<h4>This site is partially funded by the game GloamVault.</h4>
  <a href="https://store.steampowered.com/app/3460840/Gloamvault/?utm_source=dcss_site&utm_medium=referral&utm_campaign=dcss_site" target="_blank">
    <img src="/img/ads/gloamvault-lowerbanner.jpg" alt="Ad to GloamVault on Steam" loading="lazy">
  </a>
	<h4>Please visit their entry on Steam by clicking the image above to get them to increase their funding for this website.</h4>
</div>

<!-- Floating Banner Container -->
<div class="floating-banner">
	<h4>Website Sponsor</h4>
	<a href="https://store.steampowered.com/app/3460840/Gloamvault/?utm_source=dcss_site&utm_medium=referral&utm_campaign=dcss_site" target="_blank">
        <img src="/img/ads/gloamvault-rightbanner.gif" alt="GloatVault image" title="This site is partially funded by the game GloamVault. Please visit their entry on Steam by clicking the image abouve to get them to increase their funding for this website.">
    </a>
	<h4>the game with DCSS sprites</h4>
    <!-- Optional Close Button -->
    <button onclick="this.parentElement.style.display='none'" class="close-btn">&times;</button>
</div>

<style>
	
	.ad-banner {
	  text-align: center;
	  margin: 20px 0;
	}
	
	.ad-banner img {
	  max-width: 100%; /* Makes it responsive */
	  height: auto;
	  border-radius: 8px;
}

    .floating-banner {
        position: fixed;
        right: 20px;       /* Distance from right edge */
        top: 50%;          /* Center vertically */
        transform: translateY(-50%); 
        z-index: 9999;     /* Keeps it on top of other content */
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .floating-banner img {
        display: block;
        width: 160px;      /* Standard wide skyscraper width */
        height: auto;
    }

    .close-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #000;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 16px;
    }

    /* Hide on small screens to avoid covering content */
    @media (max-width: 1024px) {
        .floating-banner {
            display: none;
        }
    }
</style>
