--
-- PostgreSQL database dump
--

-- Dumped from database version 12.3
-- Dumped by pg_dump version 12.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: book; Type: TABLE; Schema: public; Owner: romainambroise
--

CREATE TABLE public.book (
    id integer NOT NULL,
    title character varying(75),
    image character varying(75),
    author character varying(75),
    description character varying(500),
    owner integer,
    date_ajout character varying(10)
);


ALTER TABLE public.book OWNER TO romainambroise;

--
-- Name: book_id_seq; Type: SEQUENCE; Schema: public; Owner: romainambroise
--

CREATE SEQUENCE public.book_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_id_seq OWNER TO romainambroise;

--
-- Name: book_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: romainambroise
--

ALTER SEQUENCE public.book_id_seq OWNED BY public.book.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: romainambroise
--

CREATE TABLE public.users (
    id integer NOT NULL,
    ad_level integer,
    username character varying(30),
    password character varying(160),
    email character varying(50),
    adresse character varying(150),
    datedenaissance character varying(10)
);


ALTER TABLE public.users OWNER TO romainambroise;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: romainambroise
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO romainambroise;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: romainambroise
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: book id; Type: DEFAULT; Schema: public; Owner: romainambroise
--

ALTER TABLE ONLY public.book ALTER COLUMN id SET DEFAULT nextval('public.book_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: romainambroise
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: book; Type: TABLE DATA; Schema: public; Owner: romainambroise
--

COPY public.book (id, title, image, author, description, owner, date_ajout) FROM stdin;
1	Harry potter à lécole des sorciers	harry_potter_1.jpg	JK_Rowlling	Livre sur la magie	0	\N
2	Harry potter et la chambre des secrets	harry_potter_2.jpg	JK_Rowlling	Livre sur la magie	0	\N
3	Harry potter et le prisonnier daskaban	harry_potter_3.jpg	JK_Rowlling	Livre sur la magie	0	\N
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: romainambroise
--

COPY public.users (id, ad_level, username, password, email, adresse, datedenaissance) FROM stdin;
0	0	insert-init	lol	\N	\N	\N
3	0	Niveau0	$2y$10$XUaz1qtlR4dVLSu1hmzW4ewYo0gXjw5FO3gTEU8ou7P3zcE41H4om	test@gmail.com	20 rue des football	1/1/1999
2	1	Niveau1	$2y$10$UX0yMYyUV9qKPl4kYS0Pl.DrRSUK2FD1GyazWEL8Sp.n4rgOtSbf6	test@gmail.com	40 rue des courriers	1/1/1995
1	2	Niveau2	$2y$10$DVyfA1rRtm6iAfCwkrGssu2ZZ.zB9CTv1kI6RqDLtEyI6rEUvX2f.	test@gmail.com	40 rue des sapins	1/1/1990
\.


--
-- Name: book_id_seq; Type: SEQUENCE SET; Schema: public; Owner: romainambroise
--

SELECT pg_catalog.setval('public.book_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: romainambroise
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: book book_pkey; Type: CONSTRAINT; Schema: public; Owner: romainambroise
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT book_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: romainambroise
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: book book_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: romainambroise
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT book_owner_fkey FOREIGN KEY (owner) REFERENCES public.users(id);


--
-- PostgreSQL database dump complete
--

