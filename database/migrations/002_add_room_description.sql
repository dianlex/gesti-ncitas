USE citas;

ALTER TABLE rooms
    ADD COLUMN description VARCHAR(500) NULL AFTER name;