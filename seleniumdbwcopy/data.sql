
CREATE TABLE User (
        Sid INT(11) AUTO_INCREMENT PRIMARY KEY,
        Phoneno bigint UNIQUE,
        Cntorders bigint
    );
    
CREATE TABLE Cart (
        Sid INT(11),
        orderid DATETIME ,
        sval float ,
        mval float ,
        FOREIGN KEY (Sid) REFERENCES User(Sid)
    );

drop trigger if exists before_insert_user;
delimiter //
    -- Create the trigger for before insert into user table
    CREATE TRIGGER before_insert_user
    BEFORE INSERT ON User
    FOR EACH ROW
    BEGIN
        DECLARE cnt int;
        SELECT COUNT(*) INTO cnt FROM Cart WHERE Sid = NEW.Sid;
        IF cnt IS NULL THEN
            SET NEW.Cntorders = 0;
        ELSE
            SET NEW.Cntorders = cnt;
        END IF;
    END//

    -- Create the trigger for after insert into cart table
drop trigger if exists before_insert_cart;
    CREATE TRIGGER before_insert_cart
    before INSERT ON Cart
    FOR EACH ROW
    BEGIN
        UPDATE User SET Cntorders = Cntorders + 1 WHERE Sid = NEW.Sid;
    END//

delimiter ;