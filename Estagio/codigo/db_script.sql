create table alertconditions
(
    id             int auto_increment
        primary key,
    sensor_id      int                                                   not null,
    condition_type varchar(50)                                           not null,
    value          decimal(10, 2)                                        not null,
    created_at     timestamp                   default CURRENT_TIMESTAMP null,
    type           varchar(30)                                           null,
    status         enum ('active', 'inactive') default 'active'          null
);

create table espacos
(
    id        int auto_increment
        primary key,
    nome      varchar(255) null,
    descricao text         null
);

create table organizadores
(
    id            int auto_increment
        primary key,
    email         varchar(255)         not null,
    password      varchar(255)         not null,
    name          varchar(255)         not null,
    is_superadmin tinyint(1) default 0 not null,
    constraint email
        unique (email)
);

create table plantas
(
    id          int auto_increment
        primary key,
    planta_path varchar(255) not null
);

create table polygons
(
    id         int auto_increment
        primary key,
    space_id   int          not null,
    sensor_ids varchar(144) null,
    space_name varchar(255) null,
    points     text         not null,
    plant_id   int          not null,
    constraint polygons_pk
        unique (space_id),
    constraint fk_plant
        foreign key (plant_id) references plantas (id),
    constraint polygons_ibfk_1
        foreign key (space_id) references espacos (id)
);

create index space_id_index
    on polygons (space_id);

create table sensors
(
    id         int auto_increment
        primary key,
    location   varchar(255)                          not null,
    status     varchar(50) default 'ativo'           null,
    created_at timestamp   default CURRENT_TIMESTAMP null,
    updated_at timestamp   default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP,
    username   varchar(255)                          not null,
    password   varchar(255)                          not null
);

create table espacos_sensores
(
    id        int auto_increment
        primary key,
    espaco_id int null,
    sensor_id int null,
    constraint espacos_sensores_ibfk_1
        foreign key (espaco_id) references espacos (id),
    constraint espacos_sensores_ibfk_2
        foreign key (sensor_id) references sensors (id)
);

create index espaco_id
    on espacos_sensores (espaco_id);

create index sensor_id
    on espacos_sensores (sensor_id);

create table sensordata
(
    id          int auto_increment
        primary key,
    sensor_id   int                                 not null,
    temperature float                               not null,
    humidity    float                               not null,
    noise       float                               not null,
    air_quality float                               not null,
    timestamp   timestamp default CURRENT_TIMESTAMP null,
    constraint sensordata_ibfk_1
        foreign key (sensor_id) references sensors (id)
);

create index sensor_id
    on sensordata (sensor_id);