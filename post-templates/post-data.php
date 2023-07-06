<?php get_header(); ?>
<style>
    .CTA {
        display:none;
    }
	.icon-image img {
	max-width: 40px;
	}

	.icon-image p {
		margin-left: 10px;
	}
	.db-container {
    	max-width: 900px;
	}
	.icon-image-row {
		display: flex;
		align-items: center;
		gap: 10px;
	}
	.icon-image-row p,
	.icon-image-row img {
		margin: 0;
		padding: 0;
	}
	.icon-image-row img {
    display: inline-block;
}
</style>
    <section class="Hero">
        <div id="blogpost" class="container-fluid">	
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
			
			$post_id = get_the_ID(); // get the ID of the current post
			$custom_fields = get_post_meta($post_id); // get an array of all custom fields
			
			foreach ($custom_fields as $field_name => $field_value) {
				if (strpos($field_name, 'itemdb_') === 0) { // Check if the custom field name starts with 'itemdb_'
					// Remove 'itemdb_' from the start of the field name
					$clean_field_name = str_replace('itemdb_', '', $field_name);
					echo '<p><strong>' . esc_html($clean_field_name) . ':</strong> ' . esc_html($field_value[0]) . '</p>';
				}
			}
			
			?>
			
			
			<div class="mt-2 container bg-light">
				<div style="" class="row p-5 my-5">
					<div class="col-md-9">
						<h1>Dwarf Lop</h1>
						<h2>About this breed</h2>
						<p class="lead">
The Dwarf Lop rabbit is a small to medium-sized breed known for its adorable and compact appearance. With a broad, round head and lop ears that droop downwards, these rabbits have a charming and endearing look. Their dense, soft, and plush-like coat adds to their appeal, coming in various colors and patterns. Dwarf Lops are typically friendly and gentle in nature, making them excellent pets. Their sociable demeanor, combined with their manageable size and cute features, make them a popular choice for rabbit enthusiasts and families alike.</p>
						<div class="row">
							<div class="col">
								<div class="icon-image icon-image-row">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icons8-age.svg'; ?>" alt="Image 1">
									<div>
										<label>Average Lifespan:</label>
										<p>5-7 Years</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="icon-image icon-image-row">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icons8-height.svg'; ?>" alt="Image 2">
									<div>
										<label>Height:</label>
										<p>10cm</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="icon-image icon-image-row">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icons8-weight.svg'; ?>" alt="Image 3">
									<div>
										<label>Weight:</label>
										<p>5.4lbs</p>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="icon-image align-middle icon-image-row">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icons8-country.svg'; ?>" alt="Image 3">
									<div>
										<label>Origin:</label>
										<p>Canada</p>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 align-middle">
						<img class="img-fluid" src="https://www.ukpets.com/blog/wp-content/uploads/2020/05/Netherland-Dwarf-Rabbit.jpg"/>
							<ul class="list-group list-group-flush">
							<li class="list-group-item"><strong>Fur Length:</strong> Short</li>
							<li class="list-group-item"><strong>Fur Colors:</strong> Moderate</li>
							<li class="list-group-item"><strong>ARBA Recognized?: </strong> Yes</li>
							<li class="list-group-item"><strong>BRC Recognized?: </strong> No</li>							
						</ul>
					</div>					
				</div>
			</div>
	</section>

	<section class="appearance">
		<div class="my-4 py-4 container db-container">
			<div class="row align-middle">
				<div class="col">
					<h2>Appearance</h2>
					<p>Dwarf Lop rabbits are small to medium-sized with a compact and stocky build. They have a broad, round head with medium-sized, lop ears that droop downwards. Their eyes are usually large and expressive, and their face is short and wide, with well-rounded cheeks. The coat is dense, soft, and plush-like, often medium in length and velvety to the touch. Dwarf Lops come in a variety of colors and patterns, including solid colors, broken patterns, and agouti. They typically weigh between 2.5 to 4.5 pounds and are known for their gentle and friendly nature.</p>
					<p>Golden retrievers shed often and a lot, so they require regular brushing. Thanks to their breeding as hunting and waterfowl-retrieving dogs in the Scottish Highlands, their outer coat is dense and repels water. They also have a thick undercoat. Their coats can vary in texture from wavy to straight. Heavy feathering appears on their chest, the backs of their legs, and tail.</p>
				</div>
			</div>
		</div>
	</section>
	<section class="temperament">
		<div class="my-4 py-4 container db-container">
			<div class="row align-middle">
				<div class="col">
					<h2>Appearance</h2>
					<p>Adult male golden retrievers grow to be 65–75 pounds, while females are 55–65 pounds. Their coloring ranges from light golden to cream, and dark golden to golden, and their physique can vary from broad and dense to leaner and more sporty. According to AKC standards, goldens move with a smooth, powerful gait, and the feathery tail is carried, as breed fanciers say, with a "merry action."</p>
					<p>Golden retrievers shed often and a lot, so they require regular brushing. Thanks to their breeding as hunting and waterfowl-retrieving dogs in the Scottish Highlands, their outer coat is dense and repels water. They also have a thick undercoat. Their coats can vary in texture from wavy to straight. Heavy feathering appears on their chest, the backs of their legs, and tail.</p>
				</div>
			</div>
		</div>
	</section>
	<section class="care">
		<div class="my-4 py-4 container db-container">
			<div class="row align-middle">
				<div class="col">
					<h2>Appearance</h2>
					<p>Adult male golden retrievers grow to be 65–75 pounds, while females are 55–65 pounds. Their coloring ranges from light golden to cream, and dark golden to golden, and their physique can vary from broad and dense to leaner and more sporty. According to AKC standards, goldens move with a smooth, powerful gait, and the feathery tail is carried, as breed fanciers say, with a "merry action."</p>
					<p>Golden retrievers shed often and a lot, so they require regular brushing. Thanks to their breeding as hunting and waterfowl-retrieving dogs in the Scottish Highlands, their outer coat is dense and repels water. They also have a thick undercoat. Their coats can vary in texture from wavy to straight. Heavy feathering appears on their chest, the backs of their legs, and tail.</p>
				</div>
			</div>
		</div>
	</section>
	<section class="health">
		<div class="my-4 py-4 container db-container">
			<div class="row align-middle">
				<div class="col">
					<h2>Appearance</h2>
					<p>Adult male golden retrievers grow to be 65–75 pounds, while females are 55–65 pounds. Their coloring ranges from light golden to cream, and dark golden to golden, and their physique can vary from broad and dense to leaner and more sporty. According to AKC standards, goldens move with a smooth, powerful gait, and the feathery tail is carried, as breed fanciers say, with a "merry action."</p>
					<p>Golden retrievers shed often and a lot, so they require regular brushing. Thanks to their breeding as hunting and waterfowl-retrieving dogs in the Scottish Highlands, their outer coat is dense and repels water. They also have a thick undercoat. Their coats can vary in texture from wavy to straight. Heavy feathering appears on their chest, the backs of their legs, and tail.</p>
				</div>
			</div>
		</div>
	</section>
		</div>
		<?php endwhile; endif; ?>
<?php get_footer(); ?>
