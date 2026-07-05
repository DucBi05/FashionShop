<?php

include("../../config/config.php");

$order_id = $_GET['id'];

/*
|--------------------------------------------------------------------------
| ORDER INFO
|--------------------------------------------------------------------------
*/

$order_sql = "

    SELECT *
    FROM orders
    WHERE id = $order_id

";

$order_result = mysqli_query($conn, $order_sql);

$order = mysqli_fetch_assoc($order_result);

/*
|--------------------------------------------------------------------------
| ORDER ITEMS
|--------------------------------------------------------------------------
*/

$items_sql = "

SELECT

    order_items.*,
    products.name,
    products.image

FROM order_items

LEFT JOIN products
ON order_items.product_id = products.id

WHERE order_items.order_id = $order_id

";

$items = mysqli_query($conn, $items_sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Order Detail</title>

    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

    <link
    rel="stylesheet"
    href="../assets/css/admin.css">

</head>

<body>

<div class="admin-layout">

    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">

        <?php include("../includes/header.php"); ?>

        <div class="table-card">

            <h2 class="mb-4" style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 30px; position: relative;">

                Order #<?= $order['id'] ?>

            </h2>

            <!-- CUSTOMER INFO -->

            <div class="table-border">

                <h4 class="mb-3" style="font-weight: bold;">

                    Customer Information

                </h4>

                <p>
                    <strong>Name:</strong>
                    <?= $order['customer_name'] ?>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <?= $order['customer_phone'] ?>
                </p>

                <p>
                    <strong>Address:</strong>
                    <?= $order['shipping_address'] ?>
                </p>

                <p>
                    <strong>Status:</strong>

                    <span class="status-badge" style="background: <?= $order['status'] == 'Delivered' ? '#f56223' 
                    : ($order['status'] == 'Confirmed' ? '#f33d7c' 
                    : ($order['status'] == 'Shipping' ? '#1bce51' 
                    : ($order['status'] == 'Cancelled' ? '#3c7de6' : '#ecbf0e'))) ?>;">

                        <?= $order['status'] ?>

                    </span>

                </p>

            </div>

            <!-- PRODUCTS -->

            <div class="table-border mt-4">

                <h4 class="mb-4" style="font-weight: bold;">

                    Order Items

                </h4>

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $grand_total = 0;

                    while($item = mysqli_fetch_assoc($items)):

                        $line_total =
                            $item['price']
                            * $item['quantity'];

                        $grand_total += $line_total;

                    ?>

                        <tr>

                            <td>

                                <img
                                src="../../assets/uploads/products/<?= $item['image'] ?>"
                                width="70"
                                class="product-image">

                            </td>

                            <td>

                                <?= $item['name'] ?>

                            </td>

                            <td>

                                <?= number_format($item['price']) ?>đ

                            </td>

                            <td>

                                <?= $item['quantity'] ?>

                            </td>

                            <td>

                                <?= number_format($line_total) ?>đ

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

                <div class="text-end mt-3">

                    <h4 style="font-weight: bold;">

                        Total:
                        <?= number_format($grand_total) ?>đ

                    </h4>

                </div>

            </div>

            <!-- UPDATE STATUS -->

            <div class="table-border mt-4">

                <h4 class="mb-3" style="font-weight: bold;">

                    Update Status

                </h4>

                <form
                action="update_status.php"
                method="POST">

                    <input
                    type="hidden"
                    name="order_id"
                    value="<?= $order['id'] ?>">

                    <select
                    name="status"
                    class="form-select mb-3">

                        <option
                        <?= $order['status']=="Pending" ? "selected" : "" ?>>
                        Pending
                        </option>

                        <option
                        <?= $order['status']=="Confirmed" ? "selected" : "" ?>>
                        Confirmed
                        </option>

                        <option
                        <?= $order['status']=="Shipping" ? "selected" : "" ?>>
                        Shipping
                        </option>

                        <option
                        <?= $order['status']=="Delivered" ? "selected" : "" ?>>
                        Delivered
                        </option>

                        <option
                        <?= $order['status']=="Cancelled" ? "selected" : "" ?>>
                        Cancelled
                        </option>

                    </select>

                    <button
                    type="submit"
                    class="btn-product-save">

                        Update Status

                    </button>

                </form>

            </div>

        </div>

        <?php include("../includes/footer.php"); ?>

    </div>

</div>

</body>
</html>