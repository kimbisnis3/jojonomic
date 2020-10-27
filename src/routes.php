<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->post("/getListUser", function (Request $request, Response $response){
        $sql  = "SELECT * FROM user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getUser", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql  = "SELECT * FROM user JOIN company ON company.id = user.company_id WHERE user.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getListCompany", function (Request $request, Response $response){
        $sql  = "SELECT * FROM company";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getCompany", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql  = "SELECT * FROM company WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getListBudgetCompany", function (Request $request, Response $response){
        $sql  = "SELECT * FROM  company_budget JOIN company ON company.id = company_budget.company_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getBudgetCompany", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql  = "SELECT * FROM company_budget JOIN company ON company.id = company_budget.company_id WHERE company.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/getLogTransaction", function (Request $request, Response $response){
      $sql = "SELECT
              `user`.`first_name`,
              `user`.`account`,
              `company`.`name`,
              `transaction`.type,
              `transaction`.date,
              `transaction`.amount ,
              'remaining_amount'
            FROM
              `user`
              JOIN company ON company.id = USER.company_id
              JOIN `transaction` ON `transaction`.`user_id` = `user`.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post("/createUser", function (Request $request, Response $response){
        $body = $request->getParsedBody();
        $sql  = "INSERT INTO user (first_name, last_name, email, account, company_id) VALUE (:first_name, :last_name, :email, :account, :company_id)";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":first_name" => $body["first_name"],
            ":last_name" => $body["last_name"],
            ":email" => $body["email"],
            ":account" => $body["account"],
            ":company_id" => $body["company_id"]
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->post("/createCompany", function (Request $request, Response $response){
        $body = $request->getParsedBody();
        $sql  = "INSERT INTO company (name, address) VALUE (:name, :address)";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":name" => $body["name"],
            ":address" => $body["address"]
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->post("/reimburse", function (Request $request, Response $response){
        // PARAM ID, AMOUNT | ID for user id,  amount for amount
        $body   = $request->getParsedBody();
        $type   = "R";
        $user_id= $body["id"];
        $amount = $body['amount'];

        // GET COMPANY ID FROM USER ID
        $q_user = "SELECT * FROM user WHERE id = :id";
        $stmt   = $this->db->prepare($q_user);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        $comp_id= $result['id'];

        // GET CURRENT AMOUNT
        $q_cb   = "SELECT * FROM company_budget WHERE id = '$comp_id'";
        $stmt_cb= $this->db->prepare($q_cb);
        $stmt_cb->execute();
        $res_cb = $stmt_cb->fetch();
        $curr_amount = $res_cb['amount'];

        // SET NEW AMOUNT
        $new_amount = $curr_amount - $amount;

        // INSERT TRANSACTION
        $now = date("Y-m-d H:i:s");
        $q_ins_bu     = "INSERT INTO transaction (type, user_id, amount, `date`) VALUE ('$type', '$user_id', $amount, '$now')";
        $stmt_ins_bu  = $this->db->prepare($q_ins_bu);
        $stmt_ins_bu->execute();

        // UPDATE AMOUNT AT COMPANY BUDGET
        $q_comp_bu = "UPDATE company_budget SET amount='$new_amount' WHERE id='$comp_id'";
        $stmt_budg = $this->db->prepare($q_comp_bu);
        $stmt_budg->execute();

        return $response->withJson(
          [
            "status" => "success",
            "new_amount" => $new_amount,
            "type" => $type,
          ], 200);
    });

    $app->post("/disburse", function (Request $request, Response $response){
        // PARAM ID, AMOUNT | ID for user id,  amount for amount
        $body   = $request->getParsedBody();
        $type   = "C";
        $user_id= $body["id"];
        $amount = $body['amount'];

        // GET COMPANY ID FROM USER ID
        $q_user = "SELECT * FROM user WHERE id = :id";
        $stmt   = $this->db->prepare($q_user);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        $comp_id= $result['id'];

        // GET CURRENT AMOUNT
        $q_cb   = "SELECT * FROM company_budget WHERE id = '$comp_id'";
        $stmt_cb= $this->db->prepare($q_cb);
        $stmt_cb->execute();
        $res_cb = $stmt_cb->fetch();
        $curr_amount = $res_cb['amount'];

        // SET NEW AMOUNT
        $new_amount = $curr_amount - $amount;

        // INSERT TRANSACTION
        $now = date("Y-m-d H:i:s");
        $q_ins_bu     = "INSERT INTO transaction (type, user_id, amount, `date`) VALUE ('$type', '$user_id', $amount, '$now')";
        $stmt_ins_bu  = $this->db->prepare($q_ins_bu);
        $stmt_ins_bu->execute();

        // UPDATE AMOUNT AT COMPANY BUDGET
        $q_comp_bu = "UPDATE company_budget SET amount='$new_amount' WHERE id='$comp_id'";
        $stmt_budg = $this->db->prepare($q_comp_bu);
        $stmt_budg->execute();

        return $response->withJson(
          [
            "status" => "success",
            "new_amount" => $new_amount,
            "type" => $type,
          ], 200);
    });

    $app->post("/close", function (Request $request, Response $response){
        // PARAM ID, AMOUNT | ID for user id,  amount for amount
        $body   = $request->getParsedBody();
        $type   = "S";
        $user_id= $body["id"];
        $amount = $body['amount'];

        // GET COMPANY ID FROM USER ID
        $q_user = "SELECT * FROM user WHERE id = :id";
        $stmt   = $this->db->prepare($q_user);
        $stmt->execute([":id" => $body["id"]]);
        $result = $stmt->fetch();
        $comp_id= $result['id'];

        // GET CURRENT AMOUNT
        $q_cb   = "SELECT * FROM company_budget WHERE id = '$comp_id'";
        $stmt_cb= $this->db->prepare($q_cb);
        $stmt_cb->execute();
        $res_cb = $stmt_cb->fetch();
        $curr_amount = $res_cb['amount'];

        // SET NEW AMOUNT
        $new_amount = $curr_amount + $amount;

        // INSERT TRANSACTION
        $now = date("Y-m-d H:i:s");
        $q_ins_bu     = "INSERT INTO transaction (type, user_id, amount, `date`) VALUE ('$type', '$user_id', $amount, '$now')";
        $stmt_ins_bu  = $this->db->prepare($q_ins_bu);
        $stmt_ins_bu->execute();

        // UPDATE AMOUNT AT COMPANY BUDGET
        $q_comp_bu = "UPDATE company_budget SET amount='$new_amount' WHERE id='$comp_id'";
        $stmt_budg = $this->db->prepare($q_comp_bu);
        $stmt_budg->execute();

        return $response->withJson(
          [
            "status" => "success",
            "new_amount" => $new_amount,
            "type" => $type,
          ], 200);
    });

    $app->post("/updateUser", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql = "UPDATE user SET first_name=:first_name, last_name=:last_name, email=:email, account=:account, company_id=:company_id WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":first_name" => $body["first_name"],
            ":last_name" => $body["last_name"],
            ":email" => $body["email"],
            ":account" => $body["account"],
            ":company_id" => $body["company_id"],
            ":id" => $body["id"],
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->post("/updateCompany", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql = "UPDATE company SET name=:name, address=:address WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":name" => $body["name"],
            ":address" => $body["address"],
            ":id" => $body["id"],
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->post("/deleteCompany", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql  = "DELETE FROM company WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":id" => $body['id']
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    $app->post("/deleteUser", function (Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $sql  = "DELETE FROM user WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data = [
            ":id" => $body['id']
        ];
        if($stmt->execute($data))
           return $response->withJson(["status" => "success", "data" => "1"], 200);
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });
};
