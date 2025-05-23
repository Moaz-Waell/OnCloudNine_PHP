<?php
session_start();
include('../../php/config.php');


// Check if meal ID is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['attendance']) || !isset($_SESSION['phone'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$meal_id = intval($_GET['id']);

// Fetch meal details
$stmt = $con->prepare("SELECT MEAL_Name, MEAL_Description, MEAL_Price, MEAL_Icon, CATEGORY_ID FROM MEAL WHERE MEAL_ID = ?");
$stmt->bind_param("i", $meal_id);
$stmt->execute();
$meal_result = $stmt->get_result();

if ($meal_result->num_rows !== 1) {
  header("Location: home.php");
  exit();
}

$meal = $meal_result->fetch_assoc();
$meal_name = htmlspecialchars($meal['MEAL_Name']);
$meal_description = htmlspecialchars($meal['MEAL_Description']);
$meal_price = number_format($meal['MEAL_Price'], 2);
$meal_icon = htmlspecialchars($meal['MEAL_Icon']);
$category_id = $meal['CATEGORY_ID'];

// Fetch category name for image path
$stmt_category = $con->prepare("SELECT CATEGORY_Name FROM CATEGORY WHERE CATEGORY_ID = ?");
$stmt_category->bind_param("i", $category_id);
$stmt_category->execute();
$category_result = $stmt_category->get_result();
$category = $category_result->fetch_assoc();
$category_name = htmlspecialchars(strtolower($category['CATEGORY_Name']));

// Fetch ingredients for the meal
$ingredients = array();
$stmt_ingredients = $con->prepare("SELECT I.INGREDIENT_NAME FROM MEAL_INGREDIENTS MI JOIN INGREDIENTS I ON MI.INGREDIENT_ID = I.INGREDIENT_ID WHERE MI.MEAL_ID = ?");
$stmt_ingredients->bind_param("i", $meal_id);
$stmt_ingredients->execute();
$ingredients_result = $stmt_ingredients->get_result();
while ($row = $ingredients_result->fetch_assoc()) {
  $ingredients[] = htmlspecialchars($row['INGREDIENT_NAME']);
}

// Check user allergies
$allergy_warning = array();
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $stmt_allergies = $con->prepare("SELECT A.ALLERGY_Name FROM USER_ALLERGIES UA JOIN ALLERGY A ON UA.ALLERGY_ID = A.ALLERGY_ID WHERE UA.USERS_ID = ? AND UA.Has_Allergy = 'Yes'");
  $stmt_allergies->bind_param("i", $user_id);
  $stmt_allergies->execute();
  $allergies_result = $stmt_allergies->get_result();
  $user_allergies = array();
  while ($row = $allergies_result->fetch_assoc()) {
    $user_allergies[] = htmlspecialchars($row['ALLERGY_Name']);
  }

  // Case-insensitive comparison
  $ingredients_lower = array_map('strtolower', $ingredients);
  $user_allergies_lower = array_map('strtolower', $user_allergies);
  $allergy_warning_lower = array_intersect($ingredients_lower, $user_allergies_lower);

  // Map back to original case
  foreach ($allergy_warning_lower as $lower) {
    foreach ($ingredients as $ing) {
      if (strtolower($ing) === $lower) {
        $allergy_warning[] = $ing;
        break;
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $meal_name; ?> Details</title>
  <link rel="stylesheet" href="../../style/pages/user/viewMealDetails.css" />
</head>

<body>
  <div>
    <a href="categoryMeals.php?id=<?php echo $category_id; ?>" class="btn-back">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M20.3284 11.0001V13.0001L7.50011 13.0001L10.7426 16.2426L9.32842 17.6568L3.67157 12L9.32842 6.34314L10.7426 7.75735L7.49988 11.0001L20.3284 11.0001Z"
          fill="currentColor" />
      </svg>
    </a>
  </div>
  <div class="wave">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
      <path fill="#143f23" fill-opacity="1"
        d="M0,288L40,282.7C80,277,160,267,240,229.3C320,192,400,128,480,101.3C560,75,640,85,720,106.7C800,128,880,160,960,144C1040,128,1120,64,1200,80C1280,96,1360,192,1400,240L1440,288L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z">
      </path>
    </svg>
  </div>
  <section class="meal_details">
    <div class="container">
      <div class="caption">
        <div class="heading-primary meal_name"><?php echo $meal_name; ?></div>

        <div class="description meal_description">
          <?php echo $meal_description; ?>
        </div>

        <?php if (!empty($allergy_warning)): ?>
          <div class="description allergy_warning">
            <p>Allergy detected: <?php echo implode(', ', $allergy_warning); ?></p>
          </div>
        <?php endif; ?>

        <div class="ingredient">
          <p class="description">
            <i>select to remove from the meal</i>
          </p>
          <form action="../../php/addToCart.php" method="post"">
          <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">
            <input type="hidden" name="meal_id" value="<?php echo $meal_id; ?>">
            <div class="grid grid-3-cols ingredients_list">
              <?php foreach ($ingredients as $ingredient): ?>
                <label class="checkbox-container margin-bottom-2rem ">
                  <input type="checkbox" name="ingredients[]" value="<?php echo $ingredient; ?>" checked>
                  <span class="checkmark"></span>
                  <?php echo $ingredient; ?>
                </label>
              <?php endforeach; ?>
            </div>
        </div>

        <div class="heading-secondary meal_price">$<?php echo $meal_price; ?></div>

        <div class="buttons">
          <div class="quantity-selector">
            <button type="button" class="quantity__btn" onclick="changeQuantity(-1)">
              -
            </button>
            <div class="quantity__display" id="quantity">1</div>
            <button type="button" class="quantity__btn" onclick="changeQuantity(1)">
              +
            </button>
          </div>
          <input type="hidden" name="quantity" id="quantityInput" value="1">
          <button type="submit" class="btn btn--full add_to_cart">Add to Cart</button>
        </div>
        </form>
      </div>
      <img src="../../img/meals/<?php echo $category_name; ?>/<?php echo $meal_icon; ?>"
        alt="<?php echo $meal_name; ?>">
    </div>
  </section>
  <script src="../../js/quantity_counter.js"></script>
</body>

</html>